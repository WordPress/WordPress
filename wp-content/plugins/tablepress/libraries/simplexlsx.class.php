<?php
/**
 * Excel 2007-2013 Reader Class
 *
 * Based on SimpleXLSX v0.7.7 by Sergey Schuchkin.
 * @link https://github.com/shuchkin/simplexlsx/
 *
 * @package TablePress
 * @subpackage Import
 * @author Sergey Schuchkin, Tobias Bäthge
 * @since 1.1.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * PHP Excel 2007-2013 Reader Class
 * @package TablePress
 * @subpackage Import
 * @author Sergey Schuchkin, Tobias Bäthge
 * @since 1.1.0
 */
class SimpleXLSX {

	/**
	 * XML Schema URLs.
	 */
	const SCHEMA_REL_OFFICEDOCUMENT = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument';
	const SCHEMA_REL_SHAREDSTRINGS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings';
	const SCHEMA_REL_WORKSHEET = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet';
	const SCHEMA_REL_STYLES = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles';

	/**
	 * [$workbook description]
	 *
	 * @since 1.1.0
	 * @var [type]
	 */
	protected $workbook;

	/**
	 * [$sheets description]
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $sheets = array();

	/**
	 * [$styles description]
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $styles = array();

	/**
	 * [$hyperlinks description]
	 *
	 * @since 1.1.0
	 * @var [type]
	 */
	protected $hyperlinks;

	/**
	 * [$package description]
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $package = array(
		'filename' => '',
		'mtime'    => 0,
		'size'     => 0,
		'comment'  => '',
		'entries'  => array(),
	);

	/**
	 * [$sharedstrings description]
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $sharedstrings = array();

	/**
	 * [$error description]
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $error = '';

	/**
	 * [$workbook_cell_formats description]
	 *
	 * @since 1.1.0
	 * @var array
	 */
	public $workbook_cell_formats = array();

	/**
	 * [$built_in_cell_formats description]
	 *
	 * @since 1.1.0
	 * @var array
	 */
	public static $built_in_cell_formats = array(
		0  => 'General',
		1  => '0',
		2  => '0.00',
		3  => '#,##0',
		4  => '#,##0.00',
		9  => '0%',
		10 => '0.00%',
		11 => '0.00E+00',
		12 => '# ?/?',
		13 => '# ??/??',
		14 => 'mm-dd-yy',
		15 => 'd-mmm-yy',
		16 => 'd-mmm',
		17 => 'mmm-yy',
		18 => 'h:mm AM/PM',
		19 => 'h:mm:ss AM/PM',
		20 => 'h:mm',
		21 => 'h:mm:ss',
		22 => 'm/d/yy h:mm',
		37 => '#,##0 ;(#,##0)',
		38 => '#,##0 ;[Red](#,##0)',
		39 => '#,##0.00;(#,##0.00)',
		40 => '#,##0.00;[Red](#,##0.00)',
		44 => '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
		45 => 'mm:ss',
		46 => '[h]:mm:ss',
		47 => 'mmss.0',
		48 => '##0.0E+0',
		49 => '@',
		27 => '[$-404]e/m/d',
		30 => 'm/d/yy',
		36 => '[$-404]e/m/d',
		50 => '[$-404]e/m/d',
		57 => '[$-404]e/m/d',
		59 => 't0',
		60 => 't0.00',
		61 => 't#,##0',
		62 => 't#,##0.00',
		67 => 't0%',
		68 => 't0.00%',
		69 => 't# ?/?',
		70 => 't# ??/??',
	);

	/**
	 * [$datetime_format description]
	 *
	 * @since 1.8.1
	 * @var string
	 */
	public $datetime_format = 'Y-m-d H:i:s';

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 *
	 * @param string $filename [description]
	 * @param bool   $is_data  Optional. [description]
	 */
	public function __construct( $filename, $is_data = false ) {
		$this->datetime_format = get_option( 'date_format' );
		if ( $this->_unzip( $filename, $is_data ) ) {
			$this->_parse();
		}
	}

	/**
	 * [sheets description]
	 *
	 * @since 1.1.0
	 *
	 * @return [type] [description]
	 */
	public function sheets() {
		return $this->sheets;
	}

	/**
	 * [sheetsCount description]
	 *
	 * @since 1.1.0
	 *
	 * @return int Number of sheets.
	 */
	public function sheetsCount() {
		return count( $this->sheets );
	}

	/**
	 * [sheetName description]
	 *
	 * @since 1.1.0
	 *
	 * @param [type] $worksheet_id [description]
	 * @return string|bool [description]
	 */
	public function sheetName( $worksheet_id ) {
		if ( ! isset( $this->workbook->sheets->sheet ) ) {
			return false;
		}
		foreach ( $this->workbook->sheets->sheet as $s ) {
			if ( 'rId' . $worksheet_id === $s->attributes( 'r', true )->id ) {
				return (string) $s['name'];
			}
		}
		return false;
	}

	/**
	 * [sheetNames description]
	 *
	 * @since 1.1.0
	 *
	 * @return array [description]
	 */
	public function sheetNames() {
		$result = array();
		foreach ( $this->workbook->sheets->sheet as $s ) {
			$result[ substr( $s->attributes( 'r', true )->id, 3 ) ] = (string) $s['name'];
		}
		return $result;
	}

	/**
	 * [worksheet description]
	 *
	 * @since 1.1.0
	 *
	 * @param [type] $worksheet_id [description]
	 * @return [type] [description]
	 */
	public function worksheet( $worksheet_id ) {
		if ( 0 === $worksheet_id ) {
			reset( $this->sheets );
			$worksheet_id = key( $this->sheets );
		}
		if ( isset( $this->sheets[ $worksheet_id ] ) ) {
			$ws = $this->sheets[ $worksheet_id ];
			if ( isset( $ws->hyperlinks ) ) {
				$this->hyperlinks = array();
				foreach ( $ws->hyperlinks->hyperlink as $hyperlink ) {
					$this->hyperlinks[ (string) $hyperlink['ref'] ] = (string) $hyperlink['display'];
				}
			}
			return $ws;
		}
		$this->error( 'Worksheet ' . $worksheet_id . ' not found.' );
		return false;
	}

	/**
	 * [dimension description]
	 *
	 * @since 1.1.0
	 *
	 * @param int $worksheet_id Optional. [description]
	 * @return array|false [description]
	 */
	public function dimension( $worksheet_id = 0 ) {
		if ( false === ( $ws = $this->worksheet( $worksheet_id ) ) ) {
			return false;
		}

		$ref = (string) $ws->dimension['ref'];

		if ( false !== strpos( $ref, ':' ) ) {
			$d = explode( ':', $ref );
			$index = $this->_columnIndex( $d[1] );
			return array( $index[0] + 1, $index[1] + 1 );
		}

		if ( '' !== $ref ) {
			$index = $this->_columnIndex( $ref );
			return array( $index[0] + 1, $index[1] + 1 );
		}

		return array( 0, 0 );
	}

	/**
	 * [rows description]
	 *
	 * Sheets numeration: 1, 2, 3, ...
	 *
	 * @since 1.1.0
	 *
	 * @param int $worksheet_id Optional. [description]
	 * @return array|bool [description]
	 */
	public function rows( $worksheet_id = 0 ) {
		if ( false === ( $ws = $this->worksheet( $worksheet_id ) ) ) {
			return false;
		}

		$rows = array();
		$current_row = 0;

		list( $cols, ) = $this->dimension( $worksheet_id );

		foreach ( $ws->sheetData->row as $row ) {
			foreach ( $row->c as $c ) {
				list( $current_cell, ) = $this->_columnIndex( (string) $c['r'] );
				$rows[ $current_row ][ $current_cell ] = $this->value( $c );
			}
			for ( $i = 0; $i < $cols; $i++ ) {
				if ( ! isset( $rows[ $current_row ][ $i ] ) ) {
					$rows[ $current_row ][ $i ] = '';
				}
			}
			ksort( $rows[ $current_row ] );
			$current_row++;
		}
		return $rows;
	}

	/**
	 * [rowsEx description]
	 *
	 * @since 1.1.0
	 *
	 * @param int $worksheet_id Optional. [description]
	 * @return array|bool [description]
	 */
	public function rowsEx( $worksheet_id = 0 ) {
		if ( false === ( $ws = $this->worksheet( $worksheet_id ) ) ) {
			return false;
		}

		$rows = array();
		$current_row = 0;
		list( $cols, ) = $this->dimension( $worksheet_id );

		foreach ( $ws->sheetData->row as $row ) {
			foreach ( $row->c as $c ) {
				list( $current_cell, ) = $this->_columnIndex( (string) $c['r'] );
				$t = (string) $c['t'];
				$s = (int) $c['s'];
				if ( $s > 0 && isset( $this->workbook_cell_formats[ $s ] ) ) {
					$format = $this->workbook_cell_formats[ $s ]['format'];
					if ( false !== strpos( $format, 'm' ) ) {
						$t = 'd';
					}
				} else {
					$format = '';
				}

				$rows[ $current_row ][ $current_cell ] = array(
					'type'   => $t,
					'name'   => (string) $c['r'],
					'value'  => $this->value( $c, $format ),
					'href'   => $this->href( $c ),
					'f'      => (string) $c->f,
					'format' => $format,
				);
			}
			for ( $i = 0; $i < $cols; $i++ ) {
				if ( ! isset( $rows[ $current_row ][ $i ] ) ) {
					for ( $c = '', $j = $i; $j >= 0; $j = (int) ( $j / 26 ) - 1 ) {
						$c = chr( $j % 26 + 65 ) . $c;
					}

					$rows[ $current_row ][ $i ] = array(
						'type'   => '',
						// 'name' => chr( $i + 65 ) . ( $current_row + 1 ),
						'name'   => $c . ( $current_row + 1 ),
						'value'  => '',
						'href'   => '',
						'f'      => '',
						'format' => '',
					);
				}
			}
			ksort( $rows[ $current_row ] );
			$current_row++;
		}
		return $rows;
	}

	/**
	 * [_columnIndex description]
	 *
	 * @since 1.1.0
	 *
	 * @param string $cell Optional. [description]
	 * @return array [description]
	 */
	protected function _columnIndex( $cell = 'A1' ) {
		if ( preg_match( '/([A-Z]+)(\d+)/', $cell, $m ) ) {
			list( , $col, $row ) = $m;

			$colLen = strlen( $col );
			$index = 0;

			for ( $i = $colLen - 1; $i >= 0; $i-- ) {
				$index += ( ord( $col[ $i ] ) - 64 ) * pow( 26, $colLen - $i - 1 );
			}

			return array( $index - 1, $row - 1 );
		}
		$this->error( 'Invalid cell index ' . $cell );

		return false;
	}

	/**
	 * [value description]
	 *
	 * @since 1.1.0
	 *
	 * @param [type] $cell   [description]
	 * @param [type] $format [description]
	 * @return mixed [description]
	 */
	public function value( $cell, $format = null ) {
		// Determine data type.
		$dataType = (string) $cell['t'];

		if ( null === $format ) {
			$s = (int) $cell['s'];
			if ( $s > 0 && isset( $this->workbook_cell_formats[ $s ] ) ) {
				$format = $this->workbook_cell_formats[ $s ]['format'];
			}
		}
		if ( false !== strpos( $format, 'm' ) ) {
			$dataType = 'd';
		}
		$value = '';
		switch ( $dataType ) {
			case 's':
				// Value is a shared string.
				if ( '' !== (string) $cell->v ) {
					$value = $this->sharedstrings[ (int) $cell->v ];
				}
				break;
			case 'b':
				// Value is boolean.
				$value = (string) $cell->v;
				if ( '0' === $value ) {
					$value = false;
				} elseif ( '1' === $value ) {
					$value = true;
				} else {
					$value = (bool) $cell->v;
				}
				break;
			case 'inlineStr':
				// Value is rich text inline.
				$value = $this->_parseRichText( $cell->is );
				break;
			case 'e':
				// Value is an error message.
				if ( '' !== (string) $cell->v ) {
					$value = (string) $cell->v;
				}
				break;
			case 'd':
				// Value is a date.
				$value = $this->datetime_format ? gmdate( $this->datetime_format, $this->unixstamp( (float) $cell->v ) ) : (float) $cell->v;
				break;
			default:
				// Value is a string.
				$value = (string) $cell->v;
				// Check for numeric values by converting them forth and back.
				if ( is_numeric( $value ) && 's' !== $dataType ) {
					if ( $value == (int) $value ) {
						$value = (int) $value;
					} elseif ( $value == (float) $value ) {
						$value = (float) $value;
					}
				}
		}
		return $value;
	}

	/**
	 * [href description]
	 *
	 * @since 1.1.0
	 *
	 * @param [type] $cell [description]
	 * @return string [description]
	 */
	public function href( $cell ) {
		return isset( $this->hyperlinks[ (string) $cell['r'] ] ) ? $this->hyperlinks[ (string) $cell['r'] ] : '';
	}

	/**
	 * [getStyles description]
	 *
	 * @since 1.8.1
	 *
	 * @return [type] [description]
	 */
	public function getStyles() {
		return $this->styles;
	}

	/**
	 * [_unzip description]
	 *
	 * @since 1.1.0
	 *
	 * @param [type] $filename [description]
	 * @param bool   $is_data  Optional. [description]
	 * @return [type] [description]
	 */
	protected function _unzip( $filename, $is_data = false ) {
		if ( $is_data ) {
			$this->package['filename'] = 'default.xlsx';
			$this->package['mtime'] = time();
			$this->package['size'] = strlen( $filename );
			$vZ = $filename;
		} else {
			if ( ! is_readable( $filename ) ) {
				$this->error( 'File not found ' . $filename );
				return false;
			}

			// Package information.
			$this->package['filename'] = $filename;
			$this->package['mtime'] = filemtime( $filename );
			$this->package['size'] = filesize( $filename );

			// Read file.
			$vZ = file_get_contents( $filename );
		}

		/*
		// Cut end of central directory
		$aE = explode( "\x50\x4b\x05\x06", $vZ );
		if ( 1 === count( $aE ) ) {
			$this->error( 'Unknown format' );
			return false;
		}
		*/

		if ( false === ( $pcd = strrpos( $vZ, "\x50\x4b\x05\x06" ) ) ) {
			$this->error( 'Unknown archive format' );
			return false;
		}
		$aE = array(
			0 => substr( $vZ, 0, $pcd ),
			1 => substr( $vZ, $pcd + 3 ),
		);

		// Normal way.
		$aP = unpack( 'x16/v1CL', $aE[1] );
		$this->package['comment'] = substr( $aE[1], 18, $aP['CL'] );

		// Translates end of line from other operating systems.
		$this->package['comment'] = str_replace( array( "\r\n", "\r" ), "\n", $this->package['comment'] );

		// Cut the entries from the central directory.
		$aE = explode( "\x50\x4b\x01\x02", $vZ );
		// Explode to each part.
		$aE = explode( "\x50\x4b\x03\x04", $aE[0] );
		// Shift out spanning signature or empty entry.
		array_shift( $aE );

		// Loop through the entries.
		foreach ( $aE as $vZ ) {
			$aI = array();
			$aI['E'] = 0;
			$aI['EM'] = '';
			// Retrieving local file header information.
			// $aP = unpack( 'v1VN/v1GPF/v1CM/v1FT/v1FD/V1CRC/V1CS/V1UCS/v1FNL', $vZ );
			$aP = unpack( 'v1VN/v1GPF/v1CM/v1FT/v1FD/V1CRC/V1CS/V1UCS/v1FNL/v1EFL', $vZ );

			// Check if data is encrypted.
			// $bE = ( $aP['GPF'] && 0x0001 ) ? true : false;
			$bE = false;
			$nF = $aP['FNL'];
			$mF = $aP['EFL'];

			// Special case: value block after the compressed data.
			if ( $aP['GPF'] & 0x0008 ) {
				$aP1 = unpack( 'V1CRC/V1CS/V1UCS', substr( $vZ, -12 ) );
				$aP['CRC'] = $aP1['CRC'];
				$aP['CS'] = $aP1['CS'];
				$aP['UCS'] = $aP1['UCS'];
				// 2013-08-10
				$vZ = substr( $vZ, 0, -12 );
				if ( "\x50\x4b\x07\x08" === substr( $vZ, -4 ) ) {
					$vZ = substr( $vZ, 0, -4 );
				}
			}

			// Get stored filename.
			$aI['N'] = substr( $vZ, 26, $nF );

			// If it's a directory entry, it will be skipped.
			if ( '/' === substr( $aI['N'], -1 ) ) {
				continue;
			}

			// Truncate full filename in path and filename.
			$aI['P'] = dirname( $aI['N'] );
			$aI['P'] = ( '.' === $aI['P'] ) ? '' : $aI['P'];
			$aI['N'] = basename( $aI['N'] );

			$vZ = substr( $vZ, 26 + $nF + $mF );

			if ( strlen( $vZ ) !== (int) $aP['CS'] ) { // Check only if available.
				$aI['E'] = 1;
				$aI['EM'] = 'Compressed size is not equal with the value in header information.';
			} else {
				if ( $bE ) {
					$aI['E'] = 5;
					$aI['EM'] = 'File is encrypted, which is not supported by this class.';
				} else {
					switch ( $aP['CM'] ) {
						case 0: // Stored
							// Here is nothing to do, the file ist flat.
							break;
						case 8: // Deflated
							$vZ = gzinflate( $vZ );
							break;
						case 12: // BZIP2
							if ( extension_loaded( 'bz2' ) ) {
								$vZ = bzdecompress( $vZ );
							} else {
								$aI['E'] = 7;
								$aI['EM'] = 'PHP BZIP2 extension not available.';
							}
							break;
						default:
							$aI['E'] = 6;
							$aI['EM'] = "De-/Compression method {$aP['CM']} is not supported.";
					}
					if ( ! $aI['E'] ) {
						if ( false === $vZ ) {
							$aI['E'] = 2;
							$aI['EM'] = 'Decompression of data failed.';
						} else {
							if ( strlen( $vZ ) !== (int) $aP['UCS'] ) {
								$aI['E'] = 3;
								$aI['EM'] = 'Uncompressed size is not equal with the value in header information.';
							} else {
								if ( crc32( $vZ ) !== $aP['CRC'] ) {
									$aI['E'] = 4;
									$aI['EM'] = 'CRC32 checksum is not equal with the value in header information.';
								}
							}
						}
					}
				}
			}

			$aI['D'] = $vZ;

			// DOS to UNIX timestamp.
			$aI['T'] = mktime(
				( $aP['FT'] & 0xf800 ) >> 11,
				( $aP['FT'] & 0x07e0 ) >> 5,
				( $aP['FT'] & 0x001f ) << 1,
				( $aP['FD'] & 0x01e0 ) >> 5,
				( $aP['FD'] & 0x001f ),
				( ( $aP['FD'] & 0xfe00 ) >> 9 ) + 1980
			);

			// $this->Entries[] = new SimpleUnzipEntry( $aI );
			$this->package['entries'][] = array(
				'data'      => $aI['D'],
				'error'     => $aI['E'],
				'error_msg' => $aI['EM'],
				'name'      => $aI['N'],
				'path'      => $aI['P'],
				'time'      => $aI['T'],
			);

		} // end foreach entries

		return true;
	}

	/**
	 * [getPackage description]
	 *
	 * @since 1.1.0
	 *
	 * @return [type] [description]
	 */
	public function getPackage() {
		return $this->package;
	}

	/**
	 * [entryExists description]
	 *
	 * @since 1.1.0
	 *
	 * @param [type] $name [description]
	 * @return bool [description]
	 */
	public function entryExists( $name ) {
		$dir  = strtoupper( dirname( $name ) );
		$name = strtoupper( basename( $name ) );
		foreach ( $this->package['entries'] as $entry ) {
			if ( strtoupper( $entry['path'] ) === $dir && strtoupper( $entry['name'] ) === $name ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * [getEntryData description]
	 *
	 * @since 1.1.0
	 *
	 * @param [type] $name [description]
	 * @return [type] [description]
	 */
	public function getEntryData( $name ) {
		$dir  = strtoupper( dirname( $name ) );
		$name = strtoupper( basename( $name ) );
		foreach ( $this->package['entries'] as $entry ) {
			if ( strtoupper( $entry['path'] ) === $dir && strtoupper( $entry['name'] ) === $name ) {
				return $entry['data'];
			}
		}
		$this->error( 'Entry not found: ' . $name );
		return false;
	}

	/**
	 * [getEntryXML description]
	 *
	 * @since 1.1.0
	 *
	 * @param [type] $name [description]
	 * @return [type] [description]
	 */
	public function getEntryXML( $name ) {
		if ( $entry_xml = $this->getEntryData( $name ) ) {
			// XML External Entity (XXE) Prevention.
			$_old_value = libxml_disable_entity_loader( true );
			$entry_xmlobj = simplexml_load_string( $entry_xml );
			libxml_disable_entity_loader( $_old_value );
			if ( $entry_xmlobj ) {
				return $entry_xmlobj;
			}
			$e = libxml_get_last_error();
			$this->error( 'XML-entry ' . $name . ' parser error ' . $e->message . ' line ' . $e->line );
		} else {
			$this->error( 'XML-entry not found: ' . $name );
		}
		return false;
	}

	/**
	 * [unixstamp description]
	 *
	 * @since 1.1.0
	 *
	 * @param [type] $excelDateTime [description]
	 * @return [type] [description]
	 */
	public function unixstamp( $excelDateTime ) {
		$d = floor( $excelDateTime ); // seconds since 1900
		$t = $excelDateTime - $d;

		return ( abs( $d ) > 0 ) ? ( $d - 25569 ) * DAY_IN_SECONDS + round( $t * DAY_IN_SECONDS ) : round( $t * DAY_IN_SECONDS ); // 25569 days = 70 years?
	}

	/**
	 * [error description]
	 *
	 * @since 1.1.0
	 *
	 * @param string $set Optional. [description]
	 * @return [type] [description]
	 */
	public function error( $set = '' ) {
		if ( '' !== $set ) {
			$this->error = $set;
			// trigger_error( __CLASS__ . ': ' . $set, E_USER_WARNING );
		}
		return $this->error;
	}

	/**
	 * [success description]
	 *
	 * @since 1.1.0
	 *
	 * @return [type] [description]
	 */
	public function success() {
		return ! $this->error;
	}

	/**
	 * [_parse description]
	 *
	 * @since 1.1.0
	 *
	 * @return [type] [description]
	 */
	protected function _parse() {
		// Document data holders.
		$this->sharedstrings = array();
		$this->sheets = array();
		// $this->styles = array();

		// Read relations and search for officeDocument.
		if ( $relations = $this->getEntryXML( '_rels/.rels' ) ) {
			foreach ( $relations->Relationship as $rel ) {
				if ( self::SCHEMA_REL_OFFICEDOCUMENT === trim( $rel['Type'] ) ) {
					// Found officeDocument! Read workbook and relations.
					if ( $this->workbook = $this->getEntryXML( $rel['Target'] ) ) {
						if ( $workbookRelations = $this->getEntryXML( dirname( $rel['Target'] ) . '/_rels/workbook.xml.rels' ) ) {
							// Loop relations for workbook and extract sheets.
							foreach ( $workbookRelations->Relationship as $workbookRelation ) {
								$wrel_type = trim( $workbookRelation['Type'] );
								$wrel_path = dirname( trim( $rel['Target'] ) ) . '/' . trim( $workbookRelation['Target'] );
								if ( ! $this->entryExists( $wrel_path ) ) {
									continue;
								}
								if ( self::SCHEMA_REL_WORKSHEET === $wrel_type ) {
									if ( $sheet = $this->getEntryXML( $wrel_path ) ) {
										$this->sheets[ str_replace( 'rId', '', (string) $workbookRelation['Id'] ) ] = $sheet;
									}
								} elseif ( self::SCHEMA_REL_SHAREDSTRINGS === $wrel_type ) {
									if ( $sharedStrings = $this->getEntryXML( $wrel_path ) ) {
										foreach ( $sharedStrings->si as $val ) {
											if ( isset( $val->t ) ) {
												$this->sharedstrings[] = (string) $val->t;
											} elseif ( isset( $val->r ) ) {
												$this->sharedstrings[] = $this->_parseRichText( $val );
											}
										}
									}
								} elseif ( self::SCHEMA_REL_STYLES === $wrel_type ) {
									$this->styles = $this->getEntryXML( $wrel_path );

									$nf = array();
									if ( null !== $this->styles->numFmts->numFmt ) {
										foreach ( $this->styles->numFmts->numFmt as $v ) {
											$nf[ (int) $v['numFmtId'] ] = (string) $v['formatCode'];
										}
									}

									if ( null !== $this->styles->cellXfs->xf ) {
										foreach ( $this->styles->cellXfs->xf as $v ) {
											$v = (array) $v->attributes();
											$v['format'] = '';

											if ( isset( $v['@attributes']['numFmtId'] ) ) {
												$v = $v['@attributes'];
												if ( isset( self::$built_in_cell_formats[ $v['numFmtId'] ] ) ) {
													$v['format'] = self::$built_in_cell_formats[ $v['numFmtId'] ];
												} elseif ( isset( $nf[ $v['numFmtId'] ] ) ) {
													$v['format'] = $nf[ $v['numFmtId'] ];
												}
											}
											$this->workbook_cell_formats[] = $v;
										}
									}
								}
							} // foreach
							break;
						}
					}
				}
			} // foreach
		}

		// Sort sheets.
		if ( count( $this->sheets ) ) {
			ksort( $this->sheets );
			return true;
		}

		return false;
	}

	/**
	 * [_parseRichText description]
	 *
	 * @since 1.1.0
	 *
	 * @param [type] $is [description]
	 * @return string [description]
	 */
	protected function _parseRichText( $is ) {
		$value = array();

		if ( isset( $is->t ) ) {
			$value[] = (string) $is->t;
		} else {
			foreach ( $is->r as $run ) {
				$value[] = (string) $run->t;
			}
		}

		return implode( ' ', $value );
	}

	/**
	 * [parse description]
	 *
	 * @since 1.8.1
	 *
	 * @param [type] $filename [description]
	 * @param bool   $is_data  [description]
	 * @return [type] [description]
	 */
	public static function parse( $filename, $is_data = false ) {
		$xlsx = new self( $filename, $is_data );
		if ( $xlsx->success() ) {
			return $xlsx;
		}
		self::parse_error( $xlsx->error() );

		return false;
	}

	/**
	 * [parse_error description]
	 *
	 * @since 1.8.1
	 *
	 * @param string $set [description]
	 * @return [type] [description]
	 */
	public static function parse_error( $set = '' ) {
		static $error = '';
		return ( '' !== $set ) ? $error = $set : $error;
	}

	/**
	 * [getCell description]
	 *
	 * Example: xlsx->getCell(2,'B87', 0);
	 * Get cell B87 from 2nd worksheet, formatted by General (see $built_in_cell_formats for all formats).
	 * It's useful when we need to get a cell that has the wrong format,
	 * Or just for direct cell reading. (thx EGO7000)
	 *
	 * @since 1.8.1
	 *
	 * @param int      $worksheet_id.
	 * @param string   $cell.
	 * @param null|int $format.
	 *
	 * @return mixed
	 */
	public function getCell( $worksheet_id = 0, $cell = 'A1', $format = null ) {
		if ( false === ( $ws = $this->worksheet( $worksheet_id ) ) ) {
			return false;
		}

		list( $current_cell, $current_row ) = $this->_columnIndex( (string) $cell );

		$c = $ws->sheetData->row[ $current_row ]->c[ $current_cell ];
		return $this->value( $c, $format );
	}

} // class SimpleXLSX
