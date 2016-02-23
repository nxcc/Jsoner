<?php

namespace jsoner\transformer;

class WikitextTransformer extends AbstractTransformer
{
	private $url;

	/*
	$TOKEN_TABLE_START class="wikitable"
	  $TOKEN_TABLE_START_TITLE colspan="2" $TOKEN_SEPARATOR /animals
	$TOKEN_ROW_SEPARATOR
	  $TOKEN_TABLE_HEADER_START $TOKEN_SEPARATOR ID
	  $TOKEN_TABLE_HEADER_START $TOKEN_SEPARATOR TEXT
	$TOKEN_ROW_SEPARATOR
	  $TOKEN_ROW_START_SCOPED $TOKEN_SEPARATOR 303
	  $TOKEN_ROW_START Cow
	$TOKEN_ROW_SEPARATOR
	  $TOKEN_ROW_START_SCOPED $TOKEN_SEPARATOR 270
	  $TOKEN_ROW_START Rat
	$TOKEN_ROW_SEPARATOR
	  $TOKEN_ROW_START_SCOPED $TOKEN_SEPARATOR 298
	  $TOKEN_ROW_START Dog@
	$TOKEN_TABLE_END
	*/
	private static $TOKEN_TABLE_START = "{| ";
	private static $TOKEN_TABLE_END = "|}";
	private static $TOKEN_TABLE_START_TITLE = "  ! ";
	private static $TOKEN_TABLE_HEADER_START = "  ! scope=\"col\" | ";

	private static $TOKEN_ROW_START_SCOPED = "  ! scope=\"row\" | ";
	private static $TOKEN_ROW_START = "  |";
	private static $TOKEN_ROW_SEPARATOR = "|-";

	private static $TOKEN_NEWLINE = "\n";

	public function __construct( $url = null ) {
		$this->url = $url;
	}

	public function transformZero() {
		return "'''" . __METHOD__ . "'''";
	}

	public function transformOne( $json ) {
		return "'''" . __METHOD__ . "'''";
	}

	public function transformMultiple( $json ) {
		// Table
		$wikitext = self::$TOKEN_TABLE_START . "class=\"wikitable\"" . self::$TOKEN_NEWLINE;

		// Table title
		$colspan = count( $json[0] );
		$time = date( 'r' );
		$wikitext .= self::$TOKEN_TABLE_START_TITLE . "colspan=\"$colspan\" | "
				. $this->url . " @ $time" . self::$TOKEN_NEWLINE;

		// Header
		$wikitext .= self::buildWikitextHeader( $json[0] );

		foreach ( $json as $item ) {
			$wikitext .= self::buildWikitextRow( $item );
		}

		$wikitext .= self::$TOKEN_TABLE_END;
		return $wikitext;
	}

	private static function buildWikitextHeader( $item ) {
		$header = self::$TOKEN_ROW_SEPARATOR . self::$TOKEN_NEWLINE;
		foreach ( $item as $key => $value ) {
			$header .= self::$TOKEN_TABLE_HEADER_START . $key . self::$TOKEN_NEWLINE;
		}
		return $header;
	}

	private static function buildWikitextRow( $item ) {
		$firstElement = 'id';

		// The first element of every row
		$firstValue = $item[$firstElement];
		unset( $item[$firstElement] );

		$row = self::$TOKEN_ROW_SEPARATOR . self::$TOKEN_NEWLINE;
		$row .= self::$TOKEN_ROW_START_SCOPED . $firstValue . self::$TOKEN_NEWLINE;
		foreach ( $item as $key => $value ) {
			$valueRepresentation = $value;

			if ( $value === null ) {
				$valueRepresentation = ' ';
			}

			// If an item is nested, we try to create a meaningful representation
			// by trying a list of keys. If this fails, we
			$subSelectKeys = ["_title", "id"];
			if ( is_array( $value ) ) {
				foreach ( $subSelectKeys as $subSelectKey ) {
					if ( array_key_exists( $subSelectKey, $value ) ) {
						$valueRepresentation = $value[$subSelectKey];
						break; // Found a OK subselect value
					}
				}

				// We found no appropriate representation for a nested array
				if ( $valueRepresentation === null ) {
					$valueRepresentation = "'''''Verschachtelt'''''";
				}
			}

			$row .= self::$TOKEN_ROW_START . $valueRepresentation . self::$TOKEN_NEWLINE;
		}
		return $row;
	}
}
