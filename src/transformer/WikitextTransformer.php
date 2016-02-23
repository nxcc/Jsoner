<?php

namespace jsoner\transformer;

class WikitextTransformer extends AbstractTransformer
{
	public function transformZero() {
		return "'''" . __METHOD__ . "'''";
	}

	public function transformOne( $json ) {
		return "'''" . __METHOD__ . "'''";
	}

	public function transformMultiple( $json ) {
		// Table
		$wikitext = '{| class="wikitable"' . "\n";

		// Table title
		$colspan = count( $json[0] );
		$time = date( 'r' );
		$queryUrl = $this->config->getItem( "QueryUrl" );
		$wikitext .= '  ! colspan="' . $colspan . '" | ' . "$queryUrl @ $time\n";

		// Header
		$wikitext .= self::buildWikitextHeader( $json[0] );

		foreach ( $json as $item ) {
			$wikitext .= self::buildWikitextRow( $item );
		}

		$wikitext .= "|}";
		return $wikitext;
	}

	private static function buildWikitextHeader( $item ) {
		$header = "|-\n";
		foreach ( $item as $key => $value ) {
			$header .= '  ! scope="col" | ' . $key . "\n";
		}
		return $header;
	}

	private static function buildWikitextRow( $item ) {
		$row = "|-\n";

		// First element in row
		$row .= '  ! scope="row" | ' . reset( $item ) . "\n";
		unset( $item[key( $item )] );

		// Rest of the elements in a row
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

			$row .= "  | $valueRepresentation\n";
		}
		return $row;
	}
}
