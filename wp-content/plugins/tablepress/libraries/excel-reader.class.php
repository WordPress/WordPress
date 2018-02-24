<?php
/**
 * Excel 97/2003 Reader Class
 *
 * Based on PHP Excel Reader 2.21.
 * @link https://code.google.com/archive/p/php-excel-reader/
 *
 * @package TablePress
 * @subpackage Import
 * @author Matt Kruse, Matt Roxburgh, Vadim Tkachenko, Tobias Bäthge
 * @since 1.1.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * A class for reading Microsoft Excel (97/2003) Spreadsheets.
 *
 * Version 2.21
 *
 * Enhanced and maintained by Matt Kruse <https://mattkruse.com/>
 * Maintained at https://code.google.com/archive/p/php-excel-reader/
 * Licensed under MIT license
 *
 * Format parsing and MUCH more contributed by Matt Roxburgh
 *
 * Cleanup and changes for TablePress by Tobias Bäthge
 * --------------------------------------------------------------------------
 */

define( 'NUM_BIG_BLOCK_DEPOT_BLOCKS_POS', 0x2c );
define( 'SMALL_BLOCK_DEPOT_BLOCK_POS', 0x3c );
define( 'ROOT_START_BLOCK_POS', 0x30 );
define( 'BIG_BLOCK_SIZE', 0x200 );
define( 'SMALL_BLOCK_SIZE', 0x40 );
define( 'EXTENSION_BLOCK_POS', 0x44 );
define( 'NUM_EXTENSION_BLOCK_POS', 0x48 );
define( 'PROPERTY_STORAGE_BLOCK_SIZE', 0x80 );
define( 'BIG_BLOCK_DEPOT_BLOCKS_POS', 0x4c );
define( 'SMALL_BLOCK_THRESHOLD', 0x1000 );

// property storage offsets
define( 'SIZE_OF_NAME_POS', 0x40 );
define( 'TYPE_POS', 0x42 );
define( 'START_BLOCK_POS', 0x74 );
define( 'SIZE_POS', 0x78 );

define( 'IDENTIFIER_OLE', pack( 'CCCCCCCC', 0xd0, 0xcf, 0x11, 0xe0, 0xa1, 0xb1, 0x1a, 0xe1 ) );

/**
 * OLERead class
 */
class OLERead {

	/**
	 * [$data description]
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $data = '';

	/**
	 * [$error description]
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $error;

	/**
	 * [$bigBlockChain description]
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $bigBlockChain = array();

	/**
	 * [$smallBlockChain description]
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $smallBlockChain = array();

	/**
	 * [$entry description]
	 *
	 * @since 1.0.0
	 * @var [type]
	 */
	protected $entry;

	/**
	 * [$props description]
	 *
	 * @since 1.0.0
	 * @var [type]
	 */
	protected $props;

	/**
	 * [$wrkbook description]
	 *
	 * @since 1.0.0
	 * @var [type]
	 */
	protected $wrkbook;

	/**
	 * [$rootentry description]
	 *
	 * @since 1.0.0
	 * @var [type]
	 */
	protected $rootentry;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Unused.
	}

	/**
	 * [read description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $data [description]
	 * @return [type] [description]
	 */
	public function read( $data ) {
		$this->data = $data;
		if ( ! $this->data ) {
			$this->error = 1;
			return false;
		}
		if ( IDENTIFIER_OLE !== substr( $this->data, 0, 8 ) ) {
			$this->error = 2;
			return false;
		}
		$numBigBlockDepotBlocks = $this->_GetInt4d( $this->data, NUM_BIG_BLOCK_DEPOT_BLOCKS_POS );
		$sbdStartBlock = $this->_GetInt4d( $this->data, SMALL_BLOCK_DEPOT_BLOCK_POS );
		$rootStartBlock = $this->_GetInt4d( $this->data, ROOT_START_BLOCK_POS );
		$extensionBlock = $this->_GetInt4d( $this->data, EXTENSION_BLOCK_POS );
		$numExtensionBlocks = $this->_GetInt4d( $this->data, NUM_EXTENSION_BLOCK_POS );

		$bigBlockDepotBlocks = array();
		$pos = BIG_BLOCK_DEPOT_BLOCKS_POS;
		$bbdBlocks = $numBigBlockDepotBlocks;
		if ( 0 !== $numExtensionBlocks ) {
			$bbdBlocks = ( BIG_BLOCK_SIZE - BIG_BLOCK_DEPOT_BLOCKS_POS ) / 4;
		}

		for ( $i = 0; $i < $bbdBlocks; $i++ ) {
			$bigBlockDepotBlocks[ $i ] = $this->_GetInt4d( $this->data, $pos );
			$pos += 4;
		}

		for ( $j = 0; $j < $numExtensionBlocks; $j++ ) {
			$pos = ( $extensionBlock + 1 ) * BIG_BLOCK_SIZE;
			$blocksToRead = min( $numBigBlockDepotBlocks - $bbdBlocks, BIG_BLOCK_SIZE / 4 - 1 );

			for ( $i = $bbdBlocks; $i < $bbdBlocks + $blocksToRead; $i++ ) {
				$bigBlockDepotBlocks[ $i ] = $this->_GetInt4d( $this->data, $pos );
				$pos += 4;
			}

			$bbdBlocks += $blocksToRead;
			if ( $bbdBlocks < $numBigBlockDepotBlocks ) {
				$extensionBlock = $this->_GetInt4d( $this->data, $pos );
			}
		}

		// readBigBlockDepot()
		$index = 0;
		$this->bigBlockChain = array();

		for ( $i = 0; $i < $numBigBlockDepotBlocks; $i++ ) {
			$pos = ( $bigBlockDepotBlocks[ $i ] + 1 ) * BIG_BLOCK_SIZE;
			for ( $j = 0; $j < BIG_BLOCK_SIZE / 4; $j++ ) {
				$this->bigBlockChain[ $index ] = $this->_GetInt4d( $this->data, $pos );
				$pos += 4;
				$index++;
			}
		}

		// readSmallBlockDepot();
		$index = 0;
		$sbdBlock = $sbdStartBlock;
		$this->smallBlockChain = array();

		while ( -2 !== $sbdBlock ) {
			$pos = ( $sbdBlock + 1 ) * BIG_BLOCK_SIZE;
			for ( $j = 0; $j < BIG_BLOCK_SIZE / 4; $j++ ) {
				$this->smallBlockChain[ $index ] = $this->_GetInt4d( $this->data, $pos );
				$pos += 4;
				$index++;
			}
			$sbdBlock = $this->bigBlockChain[ $sbdBlock ];
		}

		// readData(rootStartBlock)
		$block = $rootStartBlock;
		$this->entry = $this->__readData( $block );
		$this->__readPropertySets();
	}

	/**
	 * [__readData description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $bl [description]
	 * @return [type] [description]
	 */
	protected function __readData( $bl ) {
		$block = $bl;
		$data = '';
		while ( -2 !== $block ) {
			$pos = ( $block + 1 ) * BIG_BLOCK_SIZE;
			$data = $data . substr( $this->data, $pos, BIG_BLOCK_SIZE );
			$block = $this->bigBlockChain[ $block ];
		}
		return $data;
	}

	/**
	 * [__readPropertySets description]
	 *
	 * @since 1.0.0
	 *
	 * @return [type] [description]
	 */
	protected function __readPropertySets() {
		$offset = 0;
		while ( $offset < strlen( $this->entry ) ) {
			$d = substr( $this->entry, $offset, PROPERTY_STORAGE_BLOCK_SIZE );
			$nameSize = ord( $d[ SIZE_OF_NAME_POS ] ) | ( ord( $d[ SIZE_OF_NAME_POS + 1 ] ) << 8 );
			$type = ord( $d[ TYPE_POS ] );
			$startBlock = $this->_GetInt4d( $d, START_BLOCK_POS );
			$size = $this->_GetInt4d( $d, SIZE_POS );
			$name = '';
			for ( $i = 0; $i < $nameSize; $i++ ) {
				$name .= $d[ $i ];
			}
			$name = str_replace( "\x00", '', $name );
			$this->props[] = array(
				'name'       => $name,
				'type'       => $type,
				'startBlock' => $startBlock,
				'size'       => $size,
			);
			if ( 'workbook' === strtolower( $name ) || 'book' === strtolower( $name ) ) {
				$this->wrkbook = count( $this->props ) - 1;
			}
			if ( 'Root Entry' === $name ) {
				$this->rootentry = count( $this->props ) - 1;
			}
			$offset += PROPERTY_STORAGE_BLOCK_SIZE;
		}
	}

	/**
	 * [getWorkBook description]
	 *
	 * @since 1.0.0
	 *
	 * @return [type] [description]
	 */
	public function getWorkBook() {
		if ( $this->props[ $this->wrkbook ]['size'] < SMALL_BLOCK_THRESHOLD ) {
			$rootdata = $this->__readData( $this->props[ $this->rootentry ]['startBlock'] );
			$streamData = '';
			$block = $this->props[ $this->wrkbook ]['startBlock'];
			while ( -2 !== $block ) {
				$pos = $block * SMALL_BLOCK_SIZE;
				$streamData .= substr( $rootdata, $pos, SMALL_BLOCK_SIZE );
				$block = $this->smallBlockChain[ $block ];
			}
			return $streamData;
		} else {
			$numBlocks = $this->props[ $this->wrkbook ]['size'] / BIG_BLOCK_SIZE;
			if ( 0 !== $this->props[ $this->wrkbook ]['size'] % BIG_BLOCK_SIZE ) {
				$numBlocks++;
			}

			if ( 0 === $numBlocks ) {
				return '';
			}
			$streamData = '';
			$block = $this->props[ $this->wrkbook ]['startBlock'];
			while ( -2 !== $block ) {
				$pos = ( $block + 1 ) * BIG_BLOCK_SIZE;
				$streamData .= substr( $this->data, $pos, BIG_BLOCK_SIZE );
				$block = $this->bigBlockChain[ $block ];
			}
			return $streamData;
		}
	}

	/**
	 * [_GetInt4d description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $data [description]
	 * @param [type] $pos  [description]
	 * @return [type] [description]
	 */
	protected function _GetInt4d( $data, $pos ) {
		$value = ord( $data[ $pos ] ) | ( ord( $data[ $pos + 1 ] ) << 8 ) | ( ord( $data[ $pos + 2 ] ) << 16 ) | ( ord( $data[ $pos + 3 ] ) << 24 );
		if ( $value >= 4294967294 ) {
			$value = -2;
		}
		return $value;
	}

} // class OLERead

define( 'SPREADSHEET_EXCEL_READER_BIFF8', 0x600 );
define( 'SPREADSHEET_EXCEL_READER_BIFF7', 0x500 );
define( 'SPREADSHEET_EXCEL_READER_WORKBOOKGLOBALS', 0x5 );
define( 'SPREADSHEET_EXCEL_READER_WORKSHEET', 0x10 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_BOF', 0x809 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_EOF', 0x0a );
define( 'SPREADSHEET_EXCEL_READER_TYPE_BOUNDSHEET', 0x85 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_DIMENSION', 0x200 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_ROW', 0x208 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_DBCELL', 0xd7 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_FILEPASS', 0x2f );
define( 'SPREADSHEET_EXCEL_READER_TYPE_NOTE', 0x1c );
define( 'SPREADSHEET_EXCEL_READER_TYPE_TXO', 0x1b6 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_RK', 0x7e );
define( 'SPREADSHEET_EXCEL_READER_TYPE_RK2', 0x27e );
define( 'SPREADSHEET_EXCEL_READER_TYPE_MULRK', 0xbd );
define( 'SPREADSHEET_EXCEL_READER_TYPE_MULBLANK', 0xbe );
define( 'SPREADSHEET_EXCEL_READER_TYPE_INDEX', 0x20b );
define( 'SPREADSHEET_EXCEL_READER_TYPE_SST', 0xfc );
define( 'SPREADSHEET_EXCEL_READER_TYPE_EXTSST', 0xff );
define( 'SPREADSHEET_EXCEL_READER_TYPE_CONTINUE', 0x3c );
define( 'SPREADSHEET_EXCEL_READER_TYPE_LABEL', 0x204 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_LABELSST', 0xfd );
define( 'SPREADSHEET_EXCEL_READER_TYPE_NUMBER', 0x203 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_NAME', 0x18 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_ARRAY', 0x221 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_STRING', 0x207 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_FORMULA', 0x406 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_FORMULA2', 0x6 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_FORMAT', 0x41e );
define( 'SPREADSHEET_EXCEL_READER_TYPE_XF', 0xe0 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_BOOLERR', 0x205 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_FONT', 0x0031 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_PALETTE', 0x0092 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_UNKNOWN', 0xffff );
define( 'SPREADSHEET_EXCEL_READER_TYPE_NINETEENFOUR', 0x22 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_MERGEDCELLS', 0xE5 );
define( 'SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS', 25569 );
define( 'SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS1904', 24107 );
define( 'SPREADSHEET_EXCEL_READER_MSINADAY', 86400 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_HYPER', 0x01b8 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_COLINFO', 0x7d );
define( 'SPREADSHEET_EXCEL_READER_TYPE_DEFCOLWIDTH', 0x55 );
define( 'SPREADSHEET_EXCEL_READER_TYPE_STANDARDWIDTH', 0x99 );
define( 'SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT', '%s' );

/*
* Main Class
*/
class Spreadsheet_Excel_Reader {

	/*
	 * The following four public constants were added to make data retrieval easier.
	 */

	/**
	 * [$colnames description]
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $colnames = array();

	/**
	 * [$colindexes description]
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $colindexes = array();

	/**
	 * [$standardColWidth description]
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $standardColWidth = 0;

	/**
	 * [$defaultColWidth description]
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $defaultColWidth = 0;

	/**
	 * [$store_extended_info description]
	 *
	 * @since 1.0.0
	 * @var [type]
	 */
	protected $store_extended_info;

	/**
	 * [$_encoderFunction description]
	 *
	 * @since 1.0.0
	 * @var [type]
	 */
	protected $_encoderFunction;

	/**
	 * [$nineteenFour description]
	 *
	 * @since 1.0.0
	 * @var [type]
	 */
	protected $nineteenFour;

	/**
	 * [$sn description]
	 *
	 * @since 1.0.0
	 * @var [type]
	 */
	protected $sn;

	/**
	 * [myHex description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $d [description]
	 * @return [type] [description]
	 */
	protected function myHex( $d ) {
		if ( $d < 16 ) {
			return '0' . dechex( $d );
		}
		return dechex( $d );
	}

	/**
	 * [dumpHexData description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $data   [description]
	 * @param [type] $pos    [description]
	 * @param [type] $length [description]
	 * @return [type] [description]
	 */
	protected function dumpHexData( $data, $pos, $length ) {
		$info = '';
		for ( $i = 0; $i <= $length; $i++ ) {
			if ( 0 !== $i ) {
				$info .= ' ';
			}
			$info .= $this->myHex( ord( $data[ $pos + $i ] ) ) . ( ord( $data[ $pos + $i ] ) > 31 ? '[' . $data[ $pos + $i ] . ']' : '' );
		}
		return $info;
	}

	/**
	 * [getCol description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $col [description]
	 * @return [type] [description]
	 */
	protected function getCol( $col ) {
		if ( is_string( $col ) ) {
			$col = strtolower( $col );
			if ( array_key_exists( $col, $this->colnames ) ) {
				$col = $this->colnames[ $col ];
			}
		}
		return $col;
	}

	// PUBLIC API FUNCTIONS
	// --------------------

	/**
	 * [val description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function val( $row, $col, $sheet = 0 ) {
		$col = $this->getCol( $col );
		if ( array_key_exists( $row, $this->sheets[ $sheet ]['cells'] ) && array_key_exists( $col, $this->sheets[ $sheet ]['cells'][ $row ] ) ) {
			return $this->sheets[ $sheet ]['cells'][ $row ][ $col ];
		}
		return '';
	}

	/**
	 * [value description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function value( $row, $col, $sheet = 0 ) {
		return $this->val( $row, $col, $sheet );
	}

	/**
	 * [info description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param string $type  Optional. [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function info( $row, $col, $type = '', $sheet = 0 ) {
		$col = $this->getCol( $col );
		if ( array_key_exists( 'cellsInfo', $this->sheets[ $sheet ] )
			&& array_key_exists( $row, $this->sheets[ $sheet ]['cellsInfo'] )
			&& array_key_exists( $col, $this->sheets[ $sheet ]['cellsInfo'][ $row ] )
			&& array_key_exists( $type, $this->sheets[ $sheet ]['cellsInfo'][ $row ][ $col ] ) ) {
			return $this->sheets[ $sheet ]['cellsInfo'][ $row ][ $col ][ $type ];
		}
		return '';
	}

	/**
	 * [type description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function type( $row, $col, $sheet = 0 ) {
		return $this->info( $row, $col, 'type', $sheet );
	}

	/**
	 * [raw description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function raw( $row, $col, $sheet = 0 ) {
		return $this->info( $row, $col, 'raw', $sheet );
	}

	/**
	 * [rowspan description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function rowspan( $row, $col, $sheet = 0 ) {
		$value = $this->info( $row, $col, 'rowspan', $sheet );
		if ( '' === $value ) {
			return 1;
		} else {
			$value = (int) $value;
		}
		return $value;
	}

	/**
	 * [colspan description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function colspan( $row, $col, $sheet = 0 ) {
		$value = $this->info( $row, $col, 'colspan', $sheet );
		if ( '' === $value ) {
			return 1;
		} else {
			$value = (int) $value;
		}
		return $value;
	}

	/**
	 * [hyperlink description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function hyperlink( $row, $col, $sheet = 0 ) {
		$link = $this->sheets[ $sheet ]['cellsInfo'][ $row ][ $col ]['hyperlink'];
		if ( $link ) {
			return $link['link'];
		}
		return '';
	}

	/**
	 * [rowcount description]
	 *
	 * @since 1.0.0
	 *
	 * @param int $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function rowcount( $sheet = 0 ) {
		return $this->sheets[ $sheet ]['numRows'];
	}

	/**
	 * [colcount description]
	 *
	 * @since 1.0.0
	 *
	 * @param int $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function colcount( $sheet = 0 ) {
		return $this->sheets[ $sheet ]['numCols'];
	}

	/**
	 * [colwidth description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function colwidth( $col, $sheet = 0 ) {
		// Col width is actually the width of the number 0. So we have to estimate and come close
		return $this->colInfo[ $sheet ][ $col ]['width'] / 9142 * 200;
	}

	/**
	 * [colhidden description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function colhidden( $col, $sheet = 0 ) {
		return (bool) $this->colInfo[ $sheet ][ $col ]['hidden'];
	}

	/**
	 * [rowheight description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function rowheight( $row, $sheet = 0 ) {
		return $this->rowInfo[ $sheet ][ $row ]['height'];
	}

	/**
	 * [rowhidden description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function rowhidden( $row, $sheet = 0 ) {
		return (bool) $this->rowInfo[ $sheet ][ $row ]['hidden'];
	}

	// GET THE CSS FOR FORMATTING
	// ==========================

	/**
	 * [style description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function style( $row, $col, $sheet = 0 ) {
		$css = '';
		$font = $this->font( $row, $col, $sheet );
		if ( '' !== $font ) {
			$css .= "font-family:{$font};";
		}
		$align = $this->align( $row, $col, $sheet );
		if ( '' !== $align ) {
			$css .= "text-align:{$align};";
		}
		$height = $this->height( $row, $col, $sheet );
		if ( '' !== $height ) {
			$css .= "font-size:{$height}px;";
		}
		$bgcolor = $this->bgColor( $row, $col, $sheet );
		if ( '' !== $bgcolor ) {
			$bgcolor = $this->colors[ $bgcolor ];
			$css .= "background-color:{$bgcolor};";
		}
		$color = $this->color( $row, $col, $sheet );
		if ( '' !== $color ) {
			$css .= "color:{$color};";
		}
		$bold = $this->bold( $row, $col, $sheet );
		if ( $bold ) {
			$css .= 'font-weight:bold;';
		}
		$italic = $this->italic( $row, $col, $sheet );
		if ( $italic ) {
			$css .= 'font-style:italic;';
		}
		$underline = $this->underline( $row, $col, $sheet );
		if ( $underline ) {
			$css .= 'text-decoration:underline;';
		}
		// Borders
		$bLeft = $this->borderLeft( $row, $col, $sheet );
		$bRight = $this->borderRight( $row, $col, $sheet );
		$bTop = $this->borderTop( $row, $col, $sheet );
		$bBottom = $this->borderBottom( $row, $col, $sheet );
		$bLeftCol = $this->borderLeftColor( $row, $col, $sheet );
		$bRightCol = $this->borderRightColor( $row, $col, $sheet );
		$bTopCol = $this->borderTopColor( $row, $col, $sheet );
		$bBottomCol = $this->borderBottomColor( $row, $col, $sheet );
		// Try to output the minimal required style.
		if ( '' !== $bLeft && $bLeft === $bRight && $bRight === $bTop && $bTop === $bBottom ) {
			$css .= 'border:' . $this->lineStylesCss[ $bLeft ] . ';';
		} else {
			if ( '' !== $bLeft ) {
				$css .= 'border-left:' . $this->lineStylesCss[ $bLeft ] . ';';
			}
			if ( '' !== $bRight ) {
				$css .= 'border-right:' . $this->lineStylesCss[ $bRight ] . ';';
			}
			if ( '' !== $bTop ) {
				$css .= 'border-top:' . $this->lineStylesCss[ $bTop ] . ';';
			}
			if ( '' !== $bBottom ) {
				$css .= 'border-bottom:' . $this->lineStylesCss[ $bBottom ] . ';';
			}
		}
		// Only output border colors if there is an actual border specified.
		if ( '' !== $bLeft && '' !== $bLeftCol ) {
			$css .= "border-left-color:{$bLeftCol};";
		}
		if ( '' !== $bRight && '' !== $bRightCol ) {
			$css .= "border-right-color:{$bRightCol};";
		}
		if ( '' !== $bTop && '' !== $bTopCol ) {
			$css .= "border-top-color:{$bTopCol};";
		}
		if ( '' !== $bBottom && '' !== $bBottomCol ) {
			$css .= "border-bottom-color:{$bBottomCol};";
		}

		return $css;
	}

	// FORMAT PROPERTIES
	// =================

	/**
	 * [format description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function format( $row, $col, $sheet = 0 ) {
		return $this->info( $row, $col, 'format', $sheet );
	}

	/**
	 * [formatIndex description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function formatIndex( $row, $col, $sheet = 0 ) {
		return $this->info( $row, $col, 'formatIndex', $sheet );
	}

	/**
	 * [formatColor description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function formatColor( $row, $col, $sheet = 0 ) {
		return $this->info( $row, $col, 'formatColor', $sheet );
	}

	// CELL (XF) PROPERTIES
	// ====================

	/**
	 * [xfRecord description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function xfRecord( $row, $col, $sheet = 0 ) {
		$xfIndex = $this->info( $row, $col, 'xfIndex', $sheet );
		if ( '' !== $xfIndex ) {
			return $this->xfRecords[ $xfIndex ];
		}
		return null;
	}

	/**
	 * [xfProperty description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param [type] $sheet [description]
	 * @param [type] $prop  [description]
	 * @return [type] [description]
	 */
	public function xfProperty( $row, $col, $sheet, $prop ) {
		$xfRecord = $this->xfRecord( $row, $col, $sheet );
		if ( null !== $xfRecord ) {
			return $xfRecord[ $prop ];
		}
		return '';
	}

	/**
	 * [align description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function align( $row, $col, $sheet = 0 ) {
		return $this->xfProperty( $row, $col, $sheet, 'align' );
	}

	/**
	 * [bgColor description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function bgColor( $row, $col, $sheet = 0 ) {
		return $this->xfProperty( $row, $col, $sheet, 'bgColor' );
	}

	/**
	 * [borderLeft description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function borderLeft( $row, $col, $sheet = 0 ) {
		return $this->xfProperty( $row, $col, $sheet, 'borderLeft' );
	}

	/**
	 * [borderRight description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function borderRight( $row, $col, $sheet = 0 ) {
		return $this->xfProperty( $row, $col, $sheet, 'borderRight' );
	}

	/**
	 * [borderTop description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function borderTop( $row, $col, $sheet = 0 ) {
		return $this->xfProperty( $row, $col, $sheet, 'borderTop' );
	}

	/**
	 * [borderBottom description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function borderBottom( $row, $col, $sheet = 0 ) {
		return $this->xfProperty( $row, $col, $sheet, 'borderBottom' );
	}

	/**
	 * [borderLeftColor description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function borderLeftColor( $row, $col, $sheet = 0 ) {
		return $this->colors[ $this->xfProperty( $row, $col, $sheet, 'borderLeftColor' ) ];
	}

	/**
	 * [borderRightColor description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function borderRightColor( $row, $col, $sheet = 0 ) {
		return $this->colors[ $this->xfProperty( $row, $col, $sheet, 'borderRightColor' ) ];
	}

	/**
	 * [borderTopColor description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function borderTopColor( $row, $col, $sheet = 0 ) {
		return $this->colors[ $this->xfProperty( $row, $col, $sheet, 'borderTopColor' ) ];
	}

	/**
	 * [borderBottomColor description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function borderBottomColor( $row, $col, $sheet = 0 ) {
		return $this->colors[ $this->xfProperty( $row, $col, $sheet, 'borderBottomColor' ) ];
	}

	// FONT PROPERTIES
	// ===============

	/**
	 * [fontRecord description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function fontRecord( $row, $col, $sheet = 0 ) {
		$xfRecord = $this->xfRecord( $row, $col, $sheet );
		if ( null !== $xfRecord ) {
			$font = $xfRecord['fontIndex'];
			if ( null !== $font ) {
				return $this->fontRecords[ $font ];
			}
		}
		return null;
	}

	/**
	 * [fontProperty description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @param [type] $prop  [description]
	 * @return [type] [description]
	 */
	public function fontProperty( $row, $col, $sheet = 0, $prop ) {
		$font = $this->fontRecord( $row, $col, $sheet );
		if ( null !== $font ) {
			return $font[ $prop ];
		}
		return false;
	}

	/**
	 * [fontIndex description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function fontIndex( $row, $col, $sheet = 0 ) {
		return $this->xfProperty( $row, $col, $sheet, 'fontIndex' );
	}

	/**
	 * [color description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function color( $row, $col, $sheet = 0 ) {
		$formatColor = $this->formatColor( $row, $col, $sheet );
		if ( '' !== $formatColor ) {
			return $formatColor;
		}
		$ci = $this->fontProperty( $row, $col, $sheet, 'color' );
		return $this->rawColor( $ci );
	}

	/**
	 * [rawColor description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $ci [description]
	 * @return [type] [description]
	 */
	public function rawColor( $ci ) {
		if ( 0x7FFF !== $ci && '' !== $ci ) {
			return $this->colors[ $ci ];
		}
		return '';
	}

	/**
	 * [bold description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function bold( $row, $col, $sheet = 0 ) {
		return $this->fontProperty( $row, $col, $sheet, 'bold' );
	}

	/**
	 * [italic description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function italic( $row, $col, $sheet = 0 ) {
		return $this->fontProperty( $row, $col, $sheet, 'italic' );
	}

	/**
	 * [underline description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function underline( $row, $col, $sheet = 0 ) {
		return $this->fontProperty( $row, $col, $sheet, 'under' );
	}

	/**
	 * [height description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function height( $row, $col, $sheet = 0 ) {
		return $this->fontProperty( $row, $col, $sheet, 'height' );
	}

	/**
	 * [font description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row   [description]
	 * @param [type] $col   [description]
	 * @param int    $sheet Optional. [description]
	 * @return [type] [description]
	 */
	public function font( $row, $col, $sheet = 0 ) {
		return $this->fontProperty( $row, $col, $sheet, 'font' );
	}

	// DUMP AN HTML TABLE OF THE ENTIRE XLS DATA
	// =========================================

	/**
	 * [dump description]
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $row_numbers Optional. [description]
	 * @param bool   $col_letters Optional. [description]
	 * @param int    $sheet       Optional. [description]
	 * @param string $table_class Optional. [description]
	 * @return [type] [description]
	 */
	public function dump( $row_numbers = false, $col_letters = false, $sheet = 0, $table_class = 'excel' ) {
		$out = "<table class=\"$table_class\" cellspacing=0>";
		if ( $col_letters ) {
			$out .= "<thead>\n\t<tr>";
			if ( $row_numbers ) {
				$out .= "\n\t\t<th>&nbsp</th>";
			}
			for ( $i = 1; $i <= $this->colcount( $sheet ); $i++ ) {
				$style = 'width:' . ( $this->colwidth( $i, $sheet ) ) . 'px;';
				if ( $this->colhidden( $i, $sheet ) ) {
					$style .= 'display:none;';
				}
				$out .= "\n\t\t<th style=\"$style\">" . strtoupper( $this->colindexes[ $i ] ) . '</th>';
			}
			$out .= "</tr></thead>\n";
		}

		$out .= "<tbody>\n";
		for ( $row = 1; $row <= $this->rowcount( $sheet ); $row++ ) {
			$rowheight = $this->rowheight( $row, $sheet );
			$style = 'height:' . ( $rowheight * ( 4 / 3 ) ) . 'px;';
			if ( $this->rowhidden( $row, $sheet ) ) {
				$style .= 'display:none;';
			}
			$out .= "\n\t<tr style=\"$style\">";
			if ( $row_numbers ) {
				$out .= "\n\t\t<th>{$row}</th>";
			}
			for ( $col = 1; $col <= $this->colcount( $sheet ); $col++ ) {
				// Account for Rowspans/Colspans
				$rowspan = $this->rowspan( $row, $col, $sheet );
				$colspan = $this->colspan( $row, $col, $sheet );
				for ( $i = 0; $i < $rowspan; $i++ ) {
					for ( $j = 0; $j < $colspan; $j++ ) {
						if ( $i > 0 || $j > 0 ) {
							$this->sheets[ $sheet ]['cellsInfo'][ $row + $i ][ $col + $j ]['dontprint'] = 1;
						}
					}
				}
				if ( ! $this->sheets[ $sheet ]['cellsInfo'][ $row ][ $col ]['dontprint'] ) {
					$style = $this->style( $row, $col, $sheet );
					if ( $this->colhidden( $col, $sheet ) ) {
						$style .= 'display:none;';
					}
					$out .= "\n\t\t<td style=\"$style\"" . ( $colspan > 1 ? " colspan={$colspan}" : '' ) . ( $rowspan > 1 ? " rowspan={$rowspan}" : '' ) . '>';
					$val = $this->val( $row, $col, $sheet );
					if ( '' === $val ) {
						$val = '&nbsp;';
					} else {
						$val = htmlentities( $val );
						$link = $this->hyperlink( $row, $col, $sheet );
						if ( '' !== $link ) {
							$val = "<a href=\"$link\">{$val}</a>";
						}
					}
					$out .= '<nobr>' . nl2br( $val ) . '</nobr>';
					$out .= '</td>';
				}
			}
			$out .= "</tr>\n";
		}
		$out .= '</tbody></table>';
		return $out;
	}

	// --------------
	// END PUBLIC API

	protected $boundsheets = array();
	protected $formatRecords = array();
	protected $fontRecords = array();
	protected $xfRecords = array();
	protected $colInfo = array();
	protected $rowInfo = array();

	protected $sst = array();
	protected $sheets = array();

	protected $data;
	protected $_ole;
	protected $_defaultEncoding = 'UTF-8';
	protected $_defaultFormat = SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT;
	protected $_columnsFormat = array();
	protected $_rowoffset = 1;
	protected $_coloffset = 1;

	/**
	 * List of default date formats used by Excel
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $dateFormats = array(
		0xe  => 'm/d/Y',
		0xf  => 'M-d-Y',
		0x10 => 'd-M',
		0x11 => 'M-Y',
		0x12 => 'h:i a',
		0x13 => 'h:i:s a',
		0x14 => 'H:i',
		0x15 => 'H:i:s',
		0x16 => 'd/m/Y H:i',
		0x2d => 'i:s',
		0x2e => 'H:i:s',
		0x2f => 'i:s.S',
	);

	/**
	 * Default number formats used by Excel
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $numberFormats = array(
		0x1  => '0',
		0x2  => '0.00',
		0x3  => '#,##0',
		0x4  => '#,##0.00',
		0x5  => '\$#,##0;(\$#,##0)',
		0x6  => '\$#,##0;[Red](\$#,##0)',
		0x7  => '\$#,##0.00;(\$#,##0.00)',
		0x8  => '\$#,##0.00;[Red](\$#,##0.00)',
		0x9  => '0%',
		0xa  => '0.00%',
		0xb  => '0.00E+00',
		0x25 => '#,##0;(#,##0)',
		0x26 => '#,##0;[Red](#,##0)',
		0x27 => '#,##0.00;(#,##0.00)',
		0x28 => '#,##0.00;[Red](#,##0.00)',
		0x29 => '#,##0;(#,##0)',  // Not exact
		0x2a => '\$#,##0;(\$#,##0)',  // Not exact
		0x2b => '#,##0.00;(#,##0.00)', // Not exact
		0x2c => '\$#,##0.00;(\$#,##0.00)', // Not exact
		0x30 => '##0.0E+0',
	);

	/**
	 * [$colors description]
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $colors = array(
		0x00   => '#000000',
		0x01   => '#FFFFFF',
		0x02   => '#FF0000',
		0x03   => '#00FF00',
		0x04   => '#0000FF',
		0x05   => '#FFFF00',
		0x06   => '#FF00FF',
		0x07   => '#00FFFF',
		0x08   => '#000000',
		0x09   => '#FFFFFF',
		0x0A   => '#FF0000',
		0x0B   => '#00FF00',
		0x0C   => '#0000FF',
		0x0D   => '#FFFF00',
		0x0E   => '#FF00FF',
		0x0F   => '#00FFFF',
		0x10   => '#800000',
		0x11   => '#008000',
		0x12   => '#000080',
		0x13   => '#808000',
		0x14   => '#800080',
		0x15   => '#008080',
		0x16   => '#C0C0C0',
		0x17   => '#808080',
		0x18   => '#9999FF',
		0x19   => '#993366',
		0x1A   => '#FFFFCC',
		0x1B   => '#CCFFFF',
		0x1C   => '#660066',
		0x1D   => '#FF8080',
		0x1E   => '#0066CC',
		0x1F   => '#CCCCFF',
		0x20   => '#000080',
		0x21   => '#FF00FF',
		0x22   => '#FFFF00',
		0x23   => '#00FFFF',
		0x24   => '#800080',
		0x25   => '#800000',
		0x26   => '#008080',
		0x27   => '#0000FF',
		0x28   => '#00CCFF',
		0x29   => '#CCFFFF',
		0x2A   => '#CCFFCC',
		0x2B   => '#FFFF99',
		0x2C   => '#99CCFF',
		0x2D   => '#FF99CC',
		0x2E   => '#CC99FF',
		0x2F   => '#FFCC99',
		0x30   => '#3366FF',
		0x31   => '#33CCCC',
		0x32   => '#99CC00',
		0x33   => '#FFCC00',
		0x34   => '#FF9900',
		0x35   => '#FF6600',
		0x36   => '#666699',
		0x37   => '#969696',
		0x38   => '#003366',
		0x39   => '#339966',
		0x3A   => '#003300',
		0x3B   => '#333300',
		0x3C   => '#993300',
		0x3D   => '#993366',
		0x3E   => '#333399',
		0x3F   => '#333333',
		0x40   => '#000000',
		0x41   => '#FFFFFF',
		0x43   => '#000000',
		0x4D   => '#000000',
		0x4E   => '#FFFFFF',
		0x4F   => '#000000',
		0x50   => '#FFFFFF',
		0x51   => '#000000',
		0x7FFF => '#000000',
	);

	/**
	 * [$lineStyles description]
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $lineStyles = array(
		0x00 => '',
		0x01 => 'Thin',
		0x02 => 'Medium',
		0x03 => 'Dashed',
		0x04 => 'Dotted',
		0x05 => 'Thick',
		0x06 => 'Double',
		0x07 => 'Hair',
		0x08 => 'Medium dashed',
		0x09 => 'Thin dash-dotted',
		0x0A => 'Medium dash-dotted',
		0x0B => 'Thin dash-dot-dotted',
		0x0C => 'Medium dash-dot-dotted',
		0x0D => 'Slanted medium dash-dotted',
	);

	/**
	 * [$lineStylesCss description]
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $lineStylesCss = array(
		'Thin'                       => '1px solid',
		'Medium'                     => '2px solid',
		'Dashed'                     => '1px dashed',
		'Dotted'                     => '1px dotted',
		'Thick'                      => '3px solid',
		'Double'                     => 'double',
		'Hair'                       => '1px solid',
		'Medium dashed'              => '2px dashed',
		'Thin dash-dotted'           => '1px dashed',
		'Medium dash-dotted'         => '2px dashed',
		'Thin dash-dot-dotted'       => '1px dashed',
		'Medium dash-dot-dotted'     => '2px dashed',
		'Slanted medium dash-dotted' => '2px dashed',
	);

	/**
	 * [read16bitstring description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $data  [description]
	 * @param [type] $start [description]
	 * @return [type] [description]
	 */
	protected function read16bitstring( $data, $start ) {
		$len = 0;
		while ( ord( $data[ $start + $len ] ) + ord( $data[ $start + $len + 1 ] ) > 0 ) {
			$len++;
		}
		return substr( $data, $start, $len );
	}

	/**
	 * [_format_value description]
	 * ADDED by Matt Kruse for better formatting
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $format [description]
	 * @param [type] $num    [description]
	 * @param [type] $f      [description]
	 * @return [type] [description]
	 */
	protected function _format_value( $format, $num, $f ) {
		// 49 = TEXT format
		// https://code.google.com/archive/p/php-excel-reader/issues/7
		if ( ( ! $f && '%s' === $format ) || ( 49 === (int) $f ) || ( 'GENERAL' === $format ) ) {
			return array(
				'string'      => $num,
				'formatColor' => null,
			);
		}

		// Custom pattern can be POSITIVE;NEGATIVE;ZERO
		// The "text" option as 4th parameter is not handled
		$parts = explode( ';', $format );
		$pattern = $parts[0];
		// Negative pattern
		if ( count( $parts ) > 2 && 0 === (int) $num ) {
			$pattern = $parts[2];
		}
		// Zero pattern
		if ( count( $parts ) > 1 && $num < 0 ) {
			$pattern = $parts[1];
			$num = abs( $num );
		}

		$color = '';
		$matches = array();
		$color_regex = '/^\[(BLACK|BLUE|CYAN|GREEN|MAGENTA|RED|WHITE|YELLOW)\]/i';
		if ( preg_match( $color_regex, $pattern, $matches ) ) {
			$color = strtolower( $matches[1] );
			$pattern = preg_replace( $color_regex, '', $pattern );
		}

		// In Excel formats, "_" is used to add spacing, which we can't do in HTML.
		$pattern = preg_replace( '/_./', '', $pattern );

		// Some non-number characters are escaped with \, which we don't need.
		$pattern = preg_replace( '/\\\/', '', $pattern );

		// Some non-number strings are quoted, so we'll get rid of the quotes.
		$pattern = preg_replace( '/"/', '', $pattern );

		// TEMPORARY - Convert # to 0.
		$pattern = preg_replace( '/\#/', '0', $pattern );

		// Find out if we need comma formatting.
		$has_commas = preg_match( '/,/', $pattern );
		if ( $has_commas ) {
			$pattern = preg_replace( '/,/', '', $pattern );
		}

		// Handle Percentages.
		if ( preg_match( '/\d(\%)([^\%]|$)/', $pattern, $matches ) ) {
			$num *= 100;
			$pattern = preg_replace( '/(\d)(\%)([^\%]|$)/', '$1%$3', $pattern );
		}

		// Handle the number itself.
		$number_regex = '/(\d+)(\.?)(\d*)/';
		if ( preg_match( $number_regex, $pattern, $matches ) ) {
			//$left = $matches[1];
			//$dec = $matches[2];
			$right = $matches[3];
			if ( $has_commas ) {
				$formatted = number_format( $num, strlen( $right ) );
			} else {
				$sprintf_pattern = '%1.' . strlen( $right ) . 'f';
				$formatted = sprintf( $sprintf_pattern, $num );
			}
			$pattern = preg_replace( $number_regex, $formatted, $pattern );
		}

		return array(
			'string'      => $pattern,
			'formatColor' => $color,
		);
	}

	/**
	 * [__construct description]
	 *
	 * @since 1.0.0
	 *
	 * @param string $data                Optional. [description]
	 * @param bool   $store_extended_info Optional. [description]
	 * @param string $outputEncoding      Optional. [description]
	 */
	public function __construct( $data = '', $store_extended_info = false, $outputEncoding = '' ) {
		$this->_ole = new OLERead();
		$this->setUTFEncoder( 'iconv' );
		if ( '' !== $outputEncoding ) {
			$this->setOutputEncoding( $outputEncoding );
		}
		for ( $i = 1; $i < 245; $i++ ) {
			$name = strtolower( ( ( ( $i - 1 ) / 26 >= 1 ) ? chr( ( $i - 1 ) / 26 + 64 ) : '' ) . chr( ( $i - 1 ) % 26 + 65 ) );
			$this->colnames[ $name ] = $i;
			$this->colindexes[ $i ] = $name;
		}
		$this->store_extended_info = $store_extended_info;
		if ( '' !== $data ) {
			$this->read( $data );
		}
	}

	/**
	 * Set the encoding method.
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $encoding [description]
	 */
	public function setOutputEncoding( $encoding ) {
		$this->_defaultEncoding = $encoding;
	}

	/**
	 * [setUTFEncoder description]
	 * $encoder = 'iconv' or 'mb'
	 * set iconv if you would like use 'iconv' for encode UTF-16LE to your encoding
	 * set mb if you would like use 'mb_convert_encoding' for encode UTF-16LE to your encoding
	 *
	 * @since 1.0.0
	 *
	 * @param string $encoder Optional. [description]
	 */
	public function setUTFEncoder( $encoder = 'iconv' ) {
		$this->_encoderFunction = '';
		if ( 'iconv' === $encoder ) {
			$this->_encoderFunction = function_exists( 'iconv' ) ? 'iconv' : '';
		} elseif ( 'mb' === $encoder ) {
			$this->_encoderFunction = function_exists( 'mb_convert_encoding' ) ? 'mb_convert_encoding' : '';
		}
	}

	/**
	 * [setRowColOffset description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $iOffset [description]
	 */
	public function setRowColOffset( $iOffset ) {
		$this->_rowoffset = $iOffset;
		$this->_coloffset = $iOffset;
	}

	/**
	 * Set the default number format.
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $sFormat [description]
	 */
	public function setDefaultFormat( $sFormat ) {
		$this->_defaultFormat = $sFormat;
	}

	/**
	 * Force a column to use a certain format.
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $column  [description]
	 * @param [type] $sFormat [description]
	 */
	public function setColumnFormat( $column, $sFormat ) {
		$this->_columnsFormat[ $column ] = $sFormat;
	}

	/**
	 * Read the spreadsheet file using OLE, then parse.
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $data [description]
	 * @return [type] [description]
	 */
	public function read( $data ) {
		$res = $this->_ole->read( $data );

		// oops, something goes wrong (Darko Miljanovic)
		if ( false === $res ) {
			// check error code
			if ( 1 === $this->_ole->error ) {
				die( 'Data is not readable' );
			} elseif ( 2 === $this->_ole->error ) {
				die( 'OLE error' );
			}
			// check other error codes here (e.g. bad fileformat, etc...)
		}
		$this->data = $this->_ole->getWorkBook();
		$this->_parse();
	}

	/**
	 * Parse a workbook.
	 *
	 * @since 1.0.0
	 *
	 * @return [type] [description]
	 */
	protected function _parse() {
		$pos = 0;
		$data = $this->data;

		//$code = $this->v( $data, $pos );
		$length = $this->v( $data, $pos + 2 );
		$version = $this->v( $data, $pos + 4 );
		$substreamType = $this->v( $data, $pos + 6 );
		if ( SPREADSHEET_EXCEL_READER_BIFF8 !== $version && SPREADSHEET_EXCEL_READER_BIFF7 !== $version ) {
			return false;
		}

		if ( SPREADSHEET_EXCEL_READER_WORKBOOKGLOBALS !== $substreamType ) {
			return false;
		}

		$pos += $length + 4;

		$code = $this->v( $data, $pos );
		$length = $this->v( $data, $pos + 2 );

		while ( SPREADSHEET_EXCEL_READER_TYPE_EOF !== $code ) {
			switch ( $code ) {
				case SPREADSHEET_EXCEL_READER_TYPE_SST:
					$spos = $pos + 4;
					$limitpos = $spos + $length;
					$uniqueStrings = $this->_GetInt4d( $data, $spos + 4 );
					$spos += 8;
					for ( $i = 0; $i < $uniqueStrings; $i++ ) {
						// Read in the number of characters
						if ( $spos === $limitpos ) {
							$opcode = $this->v( $data, $spos );
							$conlength = $this->v( $data, $spos + 2 );
							if ( 0x3c !== $opcode ) {
								return -1;
							}
							$spos += 4;
							$limitpos = $spos + $conlength;
						}
						$numChars = ord( $data[ $spos ] ) | ( ord( $data[ $spos + 1 ] ) << 8 );
						$spos += 2;
						$optionFlags = ord( $data[ $spos ] );
						$spos++;
						$asciiEncoding = ( 0 === ( $optionFlags & 0x01 ) );
						$extendedString = ( 0 !== ( $optionFlags & 0x04 ) );

						// See if string contains formatting information.
						$richString = ( 0 !== ( $optionFlags & 0x08 ) );

						if ( $richString ) {
							// Read in the crun
							$formattingRuns = $this->v( $data, $spos );
							$spos += 2;
						}

						if ( $extendedString ) {
							// Read in cchExtRst
							$extendedRunLength = $this->_GetInt4d( $data, $spos );
							$spos += 4;
						}

						$len = ( $asciiEncoding ) ? $numChars : $numChars * 2;
						if ( $spos + $len < $limitpos ) {
							$retstr = substr( $data, $spos, $len );
							$spos += $len;
						} else {
							// found continue
							$retstr = substr( $data, $spos, $limitpos - $spos );
							$bytesRead = $limitpos - $spos;
							$charsLeft = $numChars - ( ( $asciiEncoding ) ? $bytesRead : ( $bytesRead / 2 ) );
							$spos = $limitpos;

							while ( $charsLeft > 0 ) {
								$opcode = $this->v( $data, $spos );
								$conlength = $this->v( $data, $spos + 2 );
								if ( 0x3c !== $opcode ) {
									return -1;
								}
								$spos += 4;
								$limitpos = $spos + $conlength;
								$option = ord( $data[ $spos ] );
								$spos += 1;
								if ( $asciiEncoding && 0 === $option ) {
									$len = min( $charsLeft, $limitpos - $spos ); // min( $charsLeft, $conlength );
									$retstr .= substr( $data, $spos, $len );
									$charsLeft -= $len;
									$asciiEncoding = true;
								} elseif ( ! $asciiEncoding && 0 !== $option ) {
									$len = min( $charsLeft * 2, $limitpos - $spos ); // min( $charsLeft, $conlength );
									$retstr .= substr( $data, $spos, $len );
									$charsLeft -= $len / 2;
									$asciiEncoding = false;
								} elseif ( ! $asciiEncoding && 0 === $option ) {
									// Bummer - the string starts off as Unicode, but after the
									// continuation it is in straightforward ASCII encoding
									$len = min( $charsLeft, $limitpos - $spos ); // min( $charsLeft, $conlength );
									for ( $j = 0; $j < $len; $j++ ) {
										$retstr .= $data[ $spos + $j ] . chr( 0 );
									}
									$charsLeft -= $len;
									$asciiEncoding = false;
								} else {
									$newstr = '';
									for ( $j = 0; $j < strlen( $retstr ); $j++ ) {
										$newstr = $retstr[ $j ] . chr( 0 );
									}
									$retstr = $newstr;
									$len = min( $charsLeft * 2, $limitpos - $spos ); // min( $charsLeft, $conlength );
									$retstr .= substr( $data, $spos, $len );
									$charsLeft -= $len / 2;
									$asciiEncoding = false;
								}
								$spos += $len;
							}
						}
						$retstr = ( $asciiEncoding ) ? $retstr : $this->_encodeUTF16( $retstr );

						if ( $richString ) {
							$spos += 4 * $formattingRuns;
						}

						// For extended strings, skip over the extended string data
						if ( $extendedString ) {
							$spos += $extendedRunLength;
						}
						$this->sst[] = $retstr;
					}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_FILEPASS:
					return false;
					// break; // unreachable
				case SPREADSHEET_EXCEL_READER_TYPE_NAME:
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_FORMAT:
					$indexCode = $this->v( $data, $pos + 4 );
					if ( SPREADSHEET_EXCEL_READER_BIFF8 === $version ) {
						$numchars = $this->v( $data, $pos + 6 );
						if ( 0 === ord( $data[ $pos + 8 ] ) ) {
							$formatString = substr( $data, $pos + 9, $numchars );
						} else {
							$formatString = substr( $data, $pos + 9, $numchars * 2 );
						}
					} else {
						$numchars = ord( $data[ $pos + 6 ] );
						$formatString = substr( $data, $pos + 7, $numchars * 2 );
					}
					$this->formatRecords[ $indexCode ] = $formatString;
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_FONT:
					$height = $this->v( $data, $pos + 4 );
					$option = $this->v( $data, $pos + 6 );
					$color = $this->v( $data, $pos + 8 );
					$weight = $this->v( $data, $pos + 10 );
					$under = ord( $data[ $pos + 14 ] );
					// Font name
					$numchars = ord( $data[ $pos + 18 ] );
					if ( 0 === ( ord( $data[ $pos + 19 ] ) & 1 ) ) {
						$font = substr( $data, $pos + 20, $numchars );
					} else {
						$font = substr( $data, $pos + 20, $numchars * 2 );
						$font = $this->_encodeUTF16( $font );
					}
					$this->fontRecords[] = array(
						'height' => $height / 20,
						'italic' => (bool) ( $option & 2 ),
						'color'  => $color,
						'under'  => ( 0 !== $under ),
						'bold'   => ( 700 === $weight ),
						'font'   => $font,
						'raw'    => $this->dumpHexData( $data, $pos + 3, $length ),
					);
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_PALETTE:
					$colors = ord( $data[ $pos + 4 ] ) | ord( $data[ $pos + 5 ] ) << 8;
					for ( $coli = 0; $coli < $colors; $coli++ ) {
						$colOff = $pos + 2 + ( $coli * 4 );
						$colr = ord( $data[ $colOff ] );
						$colg = ord( $data[ $colOff + 1 ] );
						$colb = ord( $data[ $colOff + 2 ] );
						$this->colors[ 0x07 + $coli ] = '#' . $this->myhex( $colr ) . $this->myhex( $colg ) . $this->myhex( $colb );
					}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_XF:
					$fontIndexCode = ( ord( $data[ $pos + 4 ] ) | ord( $data[ $pos + 5 ] ) << 8 ) - 1;
					$fontIndexCode = max( 0, $fontIndexCode );
					$indexCode = ord( $data[ $pos + 6 ] ) | ord( $data[ $pos + 7 ] ) << 8;
					$alignbit = ord( $data[ $pos + 10 ] ) & 3;
					$bgi = ( ord( $data[ $pos + 22 ] ) | ord( $data[ $pos + 23 ] ) << 8 ) & 0x3FFF;
					$bgcolor = ( $bgi & 0x7F );
					// $bgcolor = ( $bgi & 0x3f80 ) >> 7;
					$align = '';
					if ( 3 === $alignbit ) {
						$align = 'right';
					} elseif ( 2 === $alignbit ) {
						$align = 'center';
					}
					$fillPattern = ( ord( $data[ $pos + 21 ] ) & 0xFC ) >> 2;
					if ( 0 === $fillPattern ) {
						$bgcolor = '';
					}

					$xf = array();
					$xf['formatIndex'] = $indexCode;
					$xf['align'] = $align;
					$xf['fontIndex'] = $fontIndexCode;
					$xf['bgColor'] = $bgcolor;
					$xf['fillPattern'] = $fillPattern;

					$border = ord( $data[ $pos + 14 ] ) | ( ord( $data[ $pos + 15 ] ) << 8 ) | ( ord( $data[ $pos + 16 ] ) << 16 ) | ( ord( $data[ $pos + 17 ] ) << 24 );
					$xf['borderLeft'] = $this->lineStyles[ ( $border & 0xF ) ];
					$xf['borderRight'] = $this->lineStyles[ ( $border & 0xF0 ) >> 4 ];
					$xf['borderTop'] = $this->lineStyles[ ( $border & 0xF00 ) >> 8 ];
					$xf['borderBottom'] = $this->lineStyles[ ( $border & 0xF000 ) >> 12 ];

					$xf['borderLeftColor'] = ( $border & 0x7F0000 ) >> 16;
					$xf['borderRightColor'] = ( $border & 0x3F800000 ) >> 23;
					$border = ( ord( $data[ $pos + 18 ] ) | ord( $data[ $pos + 19 ] ) << 8 );
					$xf['borderTopColor'] = ( $border & 0x7F );
					$xf['borderBottomColor'] = ( $border & 0x3F80 ) >> 7;
					if ( array_key_exists( $indexCode, $this->dateFormats ) ) {
						$xf['type'] = 'date';
						$xf['format'] = $this->dateFormats[ $indexCode ];
						if ( '' === $align ) {
							$xf['align'] = 'right';
						}
					} elseif ( array_key_exists( $indexCode, $this->numberFormats ) ) {
						$xf['type'] = 'number';
						$xf['format'] = $this->numberFormats[ $indexCode ];
						if ( '' === $align ) {
							$xf['align'] = 'right';
						}
					} else {
						$isdate = false;
						$formatstr = '';
						if ( $indexCode > 0 ) {
							if ( isset( $this->formatRecords[ $indexCode ] ) ) {
								$formatstr = $this->formatRecords[ $indexCode ];
							}
							if ( '' !== $formatstr ) {
								$tmp = preg_replace( '/\;.*/', '', $formatstr );
								$tmp = preg_replace( '/^\[[^\]]*\]/', '', $tmp );
								if ( 0 === preg_match( '/[^hmsday\/\-:\s\\\,AMP]/i', $tmp ) ) { // found day and time format
									$isdate = true;
									$formatstr = $tmp;
									$formatstr = str_replace( array( 'AM/PM', 'mmmm', 'mmm' ), array( 'a', 'F', 'M' ), $formatstr );
									// m/mm are used for both minutes and months - oh SNAP!
									// This mess tries to fix for that.
									// 'm' = minutes only if following h/hh or preceding s/ss
									$formatstr = preg_replace( '/(h:?)mm?/', '$1i', $formatstr );
									$formatstr = preg_replace( '/mm?(:?s)/', '1$1', $formatstr );
									// A single 'm' = n in PHP
									$formatstr = preg_replace( '/(^|[^m])m([^m]|$)/', '$1n$2', $formatstr );
									$formatstr = preg_replace( '/(^|[^m])m([^m]|$)/', '$1n$2', $formatstr );
									// else it's months
									$formatstr = str_replace( 'mm', 'm', $formatstr );
									// Convert single 'd' to 'j'
									$formatstr = preg_replace( '/(^|[^d])d([^d]|$)/', '$1j$2', $formatstr );
									$formatstr = str_replace( array( 'dddd', 'ddd', 'dd', 'yyyy', 'yy', 'hh', 'h' ), array( 'l', 'D', 'd', 'Y', 'y', 'H', 'g' ), $formatstr );
									$formatstr = preg_replace( '/ss?/', 's', $formatstr );
								}
							}
						}
						if ( $isdate ) {
							$xf['type'] = 'date';
							$xf['format'] = $formatstr;
							if ( '' === $align ) {
								$xf['align'] = 'right';
							}
						} else {
							// If the format string has a 0 or # in it, we'll assume it's a number.
							if ( preg_match( '/[0#]/', $formatstr ) ) {
								$xf['type'] = 'number';
								if ( '' === $align ) {
									$xf['align'] = 'right';
								}
							} else {
								$xf['type'] = 'other';
							}
							$xf['format'] = $formatstr;
							$xf['code'] = $indexCode;
						}
					}
					$this->xfRecords[] = $xf;
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_NINETEENFOUR:
					$this->nineteenFour = ( 1 === ord( $data[ $pos + 4 ] ) );
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_BOUNDSHEET:
					$rec_offset = $this->_GetInt4d( $data, $pos + 4 );
					//$rec_typeFlag = ord( $data[ $pos + 8 ] );
					//$rec_visibilityFlag = ord( $data[ $pos + 9 ] );
					$rec_length = ord( $data[ $pos + 10 ] );

					if ( SPREADSHEET_EXCEL_READER_BIFF8 === $version ) {
						$chartype = ord( $data[ $pos + 11 ] );
						if ( 0 === $chartype ) {
							$rec_name = substr( $data, $pos + 12, $rec_length );
						} else {
							$rec_name = $this->_encodeUTF16( substr( $data, $pos + 12, 2 * $rec_length ) );
						}
					} elseif ( SPREADSHEET_EXCEL_READER_BIFF7 === $version ) {
						$rec_name = substr( $data, $pos + 11, $rec_length );
					}
					$this->boundsheets[] = array(
						'name'   => $rec_name,
						'offset' => $rec_offset,
					);
					break;
			} // switch

			$pos += $length + 4;
			$code = ord( $data[ $pos ] ) | ord( $data[ $pos + 1 ] ) << 8;
			$length = ord( $data[ $pos + 2 ] ) | ord( $data[ $pos + 3 ] ) << 8;
		} // while

		foreach ( $this->boundsheets as $key => $val ) {
			$this->sn = $key;
			$this->_parsesheet( $val['offset'] );
		}
		return true;
	}

	/**
	 * Parse a worksheet.
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $spos [description]
	 * @return [type] [description]
	 */
	protected function _parsesheet( $spos ) {
		$cont = true;
		$data = $this->data;
		// read BOF
		// $code = ord( $data[ $spos ] ) | ord( $data[ $spos + 1 ] ) << 8;
		$length = ord( $data[ $spos + 2 ] ) | ord( $data[ $spos + 3 ] ) << 8;

		$version = ord( $data[ $spos + 4 ] ) | ord( $data[ $spos + 5 ] ) << 8;
		$substreamType = ord( $data[ $spos + 6 ] ) | ord( $data[ $spos + 7 ] ) << 8;

		if ( SPREADSHEET_EXCEL_READER_BIFF8 !== $version && SPREADSHEET_EXCEL_READER_BIFF7 !== $version ) {
			return -1;
		}

		if ( SPREADSHEET_EXCEL_READER_WORKSHEET !== $substreamType ) {
			return -2;
		}

		$spos += $length + 4;
		while ( $cont ) {
			$lowcode = ord( $data[ $spos ] );
			if ( SPREADSHEET_EXCEL_READER_TYPE_EOF === $lowcode ) {
				break;
			}
			$code = $lowcode | ord( $data[ $spos + 1 ] ) << 8;
			$length = ord( $data[ $spos + 2 ] ) | ord( $data[ $spos + 3 ] ) << 8;
			$spos += 4;
			$this->sheets[ $this->sn ]['maxrow'] = $this->_rowoffset - 1;
			$this->sheets[ $this->sn ]['maxcol'] = $this->_coloffset - 1;
			unset( $this->rectype );
			switch ( $code ) {
				case SPREADSHEET_EXCEL_READER_TYPE_DIMENSION:
					if ( ! isset( $this->numRows ) ) {
						if ( 10 === $length || SPREADSHEET_EXCEL_READER_BIFF7 === $version ) {
							$this->sheets[ $this->sn ]['numRows'] = ord( $data[ $spos + 2 ] ) | ord( $data[ $spos + 3 ] ) << 8;
							$this->sheets[ $this->sn ]['numCols'] = ord( $data[ $spos + 6 ] ) | ord( $data[ $spos + 7 ] ) << 8;
						} else {
							$this->sheets[ $this->sn ]['numRows'] = ord( $data[ $spos + 4 ] ) | ord( $data[ $spos + 5 ] ) << 8;
							$this->sheets[ $this->sn ]['numCols'] = ord( $data[ $spos + 10 ] ) | ord( $data[ $spos + 11 ] ) << 8;
						}
					}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_MERGEDCELLS:
					$cellRanges = ord( $data[ $spos ] ) | ord( $data[ $spos + 1 ] ) << 8;
					for ( $i = 0; $i < $cellRanges; $i++ ) {
						$fr = ord( $data[ $spos + 8 * $i + 2 ] ) | ord( $data[ $spos + 8 * $i + 3 ] ) << 8;
						$lr = ord( $data[ $spos + 8 * $i + 4 ] ) | ord( $data[ $spos + 8 * $i + 5 ] ) << 8;
						$fc = ord( $data[ $spos + 8 * $i + 6 ] ) | ord( $data[ $spos + 8 * $i + 7 ] ) << 8;
						$lc = ord( $data[ $spos + 8 * $i + 8 ] ) | ord( $data[ $spos + 8 * $i + 9 ] ) << 8;
						if ( $lr - $fr > 0 ) {
							$this->sheets[ $this->sn ]['cellsInfo'][ $fr + 1 ][ $fc + 1 ]['rowspan'] = $lr - $fr + 1;
						}
						if ( $lc - $fc > 0 ) {
							$this->sheets[ $this->sn ]['cellsInfo'][ $fr + 1 ][ $fc + 1 ]['colspan'] = $lc - $fc + 1;
						}
					}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_RK:
				case SPREADSHEET_EXCEL_READER_TYPE_RK2:
					$row = ord( $data[ $spos ] ) | ord( $data[ $spos + 1 ] ) << 8;
					$column = ord( $data[ $spos + 2 ] ) | ord( $data[ $spos + 3 ] ) << 8;
					$rknum = $this->_GetInt4d( $data, $spos + 6 );
					$numValue = $this->_GetIEEE754( $rknum );
					$info = $this->_getCellDetails( $spos, $numValue, $column );
					$this->addcell( $row, $column, $info['string'], $info );
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_LABELSST:
					$row = ord( $data[ $spos ] ) | ord( $data[ $spos + 1 ] ) << 8;
					$column = ord( $data[ $spos + 2 ] ) | ord( $data[ $spos + 3 ] ) << 8;
					$xfindex = ord( $data[ $spos + 4 ] ) | ord( $data[ $spos + 5 ] ) << 8;
					$index = $this->_GetInt4d( $data, $spos + 6 );
					$this->addcell( $row, $column, $this->sst[ $index ], array( 'xfIndex' => $xfindex ) );
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_MULRK:
					$row = ord( $data[ $spos ] ) | ord( $data[ $spos + 1 ] ) << 8;
					$colFirst = ord( $data[ $spos + 2 ] ) | ord( $data[ $spos + 3 ] ) << 8;
					$colLast = ord( $data[ $spos + $length - 2 ] ) | ord( $data[ $spos + $length - 1 ] ) << 8;
					$columns = $colLast - $colFirst + 1;
					$tmppos = $spos + 4;
					for ( $i = 0; $i < $columns; $i++ ) {
						$numValue = $this->_GetIEEE754( $this->_GetInt4d( $data, $tmppos + 2 ) );
						$info = $this->_getCellDetails( $tmppos - 4, $numValue, $colFirst + $i + 1 );
						$tmppos += 6;
						$this->addcell( $row, $colFirst + $i, $info['string'], $info );
					}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_NUMBER:
					$row = ord( $data[ $spos ] ) | ord( $data[ $spos + 1 ] ) << 8;
					$column = ord( $data[ $spos + 2 ] ) | ord( $data[ $spos + 3 ] ) << 8;
					$tmp = unpack( 'ddouble', substr( $data, $spos + 6, 8 ) ); // It machine machine dependent
					if ( $this->isDate( $spos ) ) {
						$numValue = $tmp['double'];
					} else {
						$numValue = $this->createNumber( $spos );
					}
					$info = $this->_getCellDetails( $spos, $numValue, $column );
					$this->addcell( $row, $column, $info['string'], $info );
					break;

				case SPREADSHEET_EXCEL_READER_TYPE_FORMULA:
				case SPREADSHEET_EXCEL_READER_TYPE_FORMULA2:
					$row = ord( $data[ $spos ] ) | ord( $data[ $spos + 1 ] ) << 8;
					$column = ord( $data[ $spos + 2 ] ) | ord( $data[ $spos + 3 ] ) << 8;
					if ( 0 === ord( $data[ $spos + 6 ] ) && 255 === ord( $data[ $spos + 12 ] ) && 255 === ord( $data[ $spos + 13 ] ) ) {
						// String formula. Result follows in a STRING record
						// This row/col are stored to be referenced in that record
						// https://code.google.com/archive/p/php-excel-reader/issues/4
						$previousRow = $row;
						$previousCol = $column;
					} elseif ( 1 === ord( $data[ $spos + 6 ] ) && 255 === ord( $data[ $spos + 12 ] ) && 255 === ord( $data[ $spos + 13 ] ) ) {
						// Boolean formula. Result is in +2; 0 = false,1 = true
						// https://code.google.com/archive/p/php-excel-reader/issues/4
						if ( 1 === ord( $this->data[ $spos + 8 ] ) ) {
							$this->addcell( $row, $column, 'TRUE' );
						} else {
							$this->addcell( $row, $column, 'FALSE' );
						}
					} elseif ( 2 === ord( $data[ $spos + 6 ] ) && 255 === ord( $data[ $spos + 12 ] ) && 255 === ord( $data[ $spos + 13 ] ) ) {
						// Error formula. Error code is in +2;
					} elseif ( 3 === ord( $data[ $spos + 6 ] ) && 255 === ord( $data[ $spos + 12 ] ) && 255 === ord( $data[ $spos + 13 ] ) ) {
						// Formula result is a null string.
						$this->addcell( $row, $column, '' );
					} else {
						// Result is a number, so first 14 bytes are just like a _NUMBER record
						$tmp = unpack( 'ddouble', substr( $data, $spos + 6, 8 ) ); // machine dependent
						if ( $this->isDate( $spos ) ) {
							$numValue = $tmp['double'];
						} else {
							$numValue = $this->createNumber( $spos );
						}
						$info = $this->_getCellDetails( $spos, $numValue, $column );
						$this->addcell( $row, $column, $info['string'], $info );
					}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_BOOLERR:
					$row = ord( $data[ $spos ] ) | ord( $data[ $spos + 1 ] ) << 8;
					$column = ord( $data[ $spos + 2 ] ) | ord( $data[ $spos + 3 ] ) << 8;
					$string = ord( $data[ $spos + 6 ] );
					$this->addcell( $row, $column, $string );
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_STRING:
					// https://code.google.com/archive/p/php-excel-reader/issues/4
					if ( SPREADSHEET_EXCEL_READER_BIFF8 === $version ) {
						// Unicode 16 string, like an SST record
						$xpos = $spos;
						$numChars = ord( $data[ $xpos ] ) | ( ord( $data[ $xpos + 1 ] ) << 8 );
						$xpos += 2;
						$optionFlags = ord( $data[ $xpos ] );
						$xpos++;
						$asciiEncoding = ( 0 === ( $optionFlags & 0x01 ) );
						$extendedString = ( 0 !== ( $optionFlags & 0x04 ) );
						// See if string contains formatting information
						$richString = ( 0 !== ( $optionFlags & 0x08 ) );
						if ( $richString ) {
							// Read in the crun
							// $formattingRuns = ord( $data[ $xpos ] ) | ( ord( $data[ $xpos + 1 ] ) << 8 );
							$xpos += 2;
						}
						if ( $extendedString ) {
							// Read in cchExtRst
							// $extendedRunLength =$this->_GetInt4d( $this->data, $xpos );
							$xpos += 4;
						}
						$len = ( $asciiEncoding ) ? $numChars : $numChars * 2;
						$retstr = substr( $data, $xpos, $len );
						$xpos += $len;
						$retstr = ( $asciiEncoding ) ? $retstr : $this->_encodeUTF16( $retstr );
					} elseif ( SPREADSHEET_EXCEL_READER_BIFF7 === $version ) {
						// Simple byte string
						$xpos = $spos;
						$numChars = ord( $data[ $xpos ] ) | ( ord( $data[ $xpos + 1 ] ) << 8 );
						$xpos += 2;
						$retstr = substr( $data, $xpos, $numChars );
					}
					$this->addcell( $previousRow, $previousCol, $retstr );
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_ROW:
					$row = ord( $data[ $spos ] ) | ord( $data[ $spos + 1 ] ) << 8;
					$rowInfo = ord( $data[ $spos + 6 ] ) | ( ( ord( $data[ $spos + 7 ] ) << 8 ) & 0x7FFF );
					if ( ( $rowInfo & 0x8000 ) > 0 ) {
						$rowHeight = -1;
					} else {
						$rowHeight = $rowInfo & 0x7FFF;
					}
					$rowHidden = ( ord( $data[ $spos + 12 ] ) & 0x20 ) >> 5;
					$this->rowInfo[ $this->sn ][ $row + 1 ] = array(
						'height' => $rowHeight / 20,
						'hidden' => $rowHidden,
					);
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_DBCELL:
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_MULBLANK:
					$row = ord( $data[ $spos ] ) | ord( $data[ $spos + 1 ] ) << 8;
					$column = ord( $data[ $spos + 2 ] ) | ord( $data[ $spos + 3 ] ) << 8;
					$cols = ( $length / 2 ) - 3;
					for ( $c = 0; $c < $cols; $c++ ) {
						$xfindex = ord( $data[ $spos + 4 + ( $c * 2 ) ] ) | ord( $data[ $spos + 5 + ( $c * 2 ) ] ) << 8;
						$this->addcell( $row, $column + $c, '', array( 'xfIndex' => $xfindex ) );
					}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_LABEL:
					$row = ord( $data[ $spos ] ) | ord( $data[ $spos + 1 ] ) << 8;
					$column = ord( $data[ $spos + 2 ] ) | ord( $data[ $spos + 3 ] ) << 8;
					$this->addcell( $row, $column, substr( $data, $spos + 8, ord( $data[ $spos + 6 ] ) | ord( $data[ $spos + 7 ] ) << 8 ) );
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_EOF:
					$cont = false;
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_HYPER:
					// Only handle hyperlinks to a URL
					$row = ord( $this->data[ $spos ] ) | ord( $this->data[ $spos + 1 ] ) << 8;
					$row2 = ord( $this->data[ $spos + 2 ] ) | ord( $this->data[ $spos + 3 ] ) << 8;
					$column = ord( $this->data[ $spos + 4 ] ) | ord( $this->data[ $spos + 5 ] ) << 8;
					$column2 = ord( $this->data[ $spos + 6 ] ) | ord( $this->data[ $spos + 7 ] ) << 8;
					$linkdata = array();
					$flags = ord( $this->data[ $spos + 28 ] );
					$udesc = '';
					$ulink = '';
					$uloc = 32;
					$linkdata['flags'] = $flags;
					if ( ( $flags & 1 ) > 0 ) {   // is a type we understand
						// is there a description ?
						if ( 0x14 === ( $flags & 0x14 ) ) {   // has a description
							$uloc += 4;
							$descLen = ord( $this->data[ $spos + 32 ] ) | ord( $this->data[ $spos + 33 ] ) << 8;
							$udesc = substr( $this->data, $spos + $uloc, $descLen * 2 );
							$uloc += 2 * $descLen;
						}
						$ulink = $this->read16bitstring( $this->data, $spos + $uloc + 20 );
						if ( '' === $udesc ) {
							$udesc = $ulink;
						}
					}
					$linkdata['desc'] = $udesc;
					$linkdata['link'] = $this->_encodeUTF16( $ulink );
					for ( $r = $row; $r <= $row2; $r++ ) {
						for ( $c = $column; $c <= $column2; $c++ ) {
							$this->sheets[ $this->sn ]['cellsInfo'][ $r + 1 ][ $c + 1 ]['hyperlink'] = $linkdata;
						}
					}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_DEFCOLWIDTH:
					$this->defaultColWidth = ord( $data[ $spos + 4 ] ) | ord( $data[ $spos + 5 ] ) << 8;
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_STANDARDWIDTH:
					$this->standardColWidth = ord( $data[ $spos + 4 ] ) | ord( $data[ $spos + 5 ] ) << 8;
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_COLINFO:
					$colfrom = ord( $data[ $spos + 0 ] ) | ord( $data[ $spos + 1 ] ) << 8;
					$colto = ord( $data[ $spos + 2 ] ) | ord( $data[ $spos + 3 ] ) << 8;
					$cw = ord( $data[ $spos + 4 ] ) | ord( $data[ $spos + 5 ] ) << 8;
					$cxf = ord( $data[ $spos + 6 ] ) | ord( $data[ $spos + 7 ] ) << 8;
					$co = ord( $data[ $spos + 8 ] );
					for ( $coli = $colfrom; $coli <= $colto; $coli++ ) {
						$this->colInfo[ $this->sn ][ $coli + 1 ] = array(
							'width'     => $cw,
							'xf'        => $cxf,
							'hidden'    => ( $co & 0x01 ),
							'collapsed' => ( $co & 0x1000 ) >> 12,
						);
					}
					break;
				default:
					break;
			} // switch
			$spos += $length;
		} // while

		if ( ! isset( $this->sheets[ $this->sn ]['numRows'] ) ) {
			$this->sheets[ $this->sn ]['numRows'] = $this->sheets[ $this->sn ]['maxrow'];
		}
		if ( ! isset( $this->sheets[ $this->sn ]['numCols'] ) ) {
			$this->sheets[ $this->sn ]['numCols'] = $this->sheets[ $this->sn ]['maxcol'];
		}
	}

	/**
	 * [isDate description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $spos [description]
	 * @return bool [description]
	 */
	protected function isDate( $spos ) {
		$xfindex = ord( $this->data[ $spos + 4 ] ) | ord( $this->data[ $spos + 5 ] ) << 8;
		return ( 'date' === $this->xfRecords[ $xfindex ]['type'] );
	}
	/**
	 * [gmgetdate description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $ts Optional. [description]
	 * @return [type] [description]
	 */
	protected function gmgetdate( $ts = null ) {
		$k = array( 'seconds', 'minutes', 'hours', 'mday', 'wday', 'mon', 'year', 'yday', 'weekday', 'month', 0 );
		return array_combine( $k, explode( ':', gmdate( 's:i:G:j:w:n:Y:z:l:F:U', is_null( $ts ) ? time() : $ts ) ) );
	}

	/**
	 * Get the details for a particular cell
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $spos     [description]
	 * @param [type] $numValue [description]
	 * @param [type] $column   [description]
	 * @return [type] [description]
	 */
	protected function _getCellDetails( $spos, $numValue, $column ) {
		$xfindex = ord( $this->data[ $spos + 4 ] ) | ord( $this->data[ $spos + 5 ] ) << 8;
		$xfrecord = $this->xfRecords[ $xfindex ];
		$type = $xfrecord['type'];

		$format = $xfrecord['format'];
		$formatIndex = $xfrecord['formatIndex'];
		$fontIndex = $xfrecord['fontIndex'];
		$formatColor = '';

		if ( isset( $this->_columnsFormat[ $column + 1 ] ) ) {
			$format = $this->_columnsFormat[ $column + 1 ];
		}

		if ( 'date' === $type ) {
			// See https://groups.google.com/forum/#!topic/php-excel-reader-discuss/nD-XkNEtjhA
			$rectype = 'date';
			// Convert numeric value into a date
			$utcDays = floor( $numValue - ( $this->nineteenFour ? SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS1904 : SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS ) );
			$utcValue = ( $utcDays ) * SPREADSHEET_EXCEL_READER_MSINADAY;
			$dateinfo = $this->gmgetdate( $utcValue );

			$raw = $numValue;
			$fractionalDay = $numValue - floor( $numValue ) + 0.0000001; // The 0.0000001 is to fix for php/excel fractional diffs

			$totalseconds = floor( SPREADSHEET_EXCEL_READER_MSINADAY * $fractionalDay );
			$secs = $totalseconds % 60;
			$totalseconds -= $secs;
			$hours = floor( $totalseconds / ( 60 * 60 ) );
			$mins = floor( $totalseconds / 60 ) % 60;
			$string = date( $format, mktime( $hours, $mins, $secs, $dateinfo['mon'], $dateinfo['mday'], $dateinfo['year'] ) );
		} elseif ( 'number' === $type ) {
			$rectype = 'number';
			$formatted = $this->_format_value( $format, $numValue, $formatIndex );
			$string = $formatted['string'];
			$formatColor = $formatted['formatColor'];
			$raw = $numValue;
		} else {
			if ( '' === $format ) {
				$format = $this->_defaultFormat;
			}
			$rectype = 'unknown';
			$formatted = $this->_format_value( $format, $numValue, $formatIndex );
			$string = $formatted['string'];
			$formatColor = $formatted['formatColor'];
			$raw = $numValue;
		}

		return array(
			'string'      => $string,
			'raw'         => $raw,
			'rectype'     => $rectype,
			'format'      => $format,
			'formatIndex' => $formatIndex,
			'fontIndex'   => $fontIndex,
			'formatColor' => $formatColor,
			'xfIndex'     => $xfindex,
		);
	}

	/**
	 * [createNumber description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $spos [description]
	 * @return [type] [description]
	 */
	protected function createNumber( $spos ) {
		$rknumhigh = $this->_GetInt4d( $this->data, $spos + 10 );
		$rknumlow = $this->_GetInt4d( $this->data, $spos + 6 );
		$sign = ( $rknumhigh & 0x80000000 ) >> 31;
		$exp = ( $rknumhigh & 0x7ff00000 ) >> 20;
		$mantissa = ( 0x100000 | ( $rknumhigh & 0x000fffff ) );
		$mantissalow1 = ( $rknumlow & 0x80000000 ) >> 31;
		$mantissalow2 = ( $rknumlow & 0x7fffffff );
		$value = $mantissa / pow( 2, ( 20 - ( $exp - 1023 ) ) );
		if ( 0 !== $mantissalow1 ) {
			$value += 1 / pow( 2, ( 21 - ( $exp - 1023 ) ) );
		}
		$value += $mantissalow2 / pow( 2, ( 52 - ( $exp - 1023 ) ) );
		if ( $sign ) {
			$value *= -1;
		}
		return $value;
	}

	/**
	 * [addcell description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $row    [description]
	 * @param [type] $col    [description]
	 * @param [type] $string [description]
	 * @param [type] $info   Optional. [description]
	 * @return [type] [description]
	 */
	protected function addcell( $row, $col, $string, $info = null ) {
		$this->sheets[ $this->sn ]['maxrow'] = max( $this->sheets[ $this->sn ]['maxrow'], $row + $this->_rowoffset );
		$this->sheets[ $this->sn ]['maxcol'] = max( $this->sheets[ $this->sn ]['maxcol'], $col + $this->_coloffset );
		$this->sheets[ $this->sn ]['cells'][ $row + $this->_rowoffset ][ $col + $this->_coloffset ] = $string;
		if ( $this->store_extended_info && $info ) {
			foreach ( $info as $key => $val ) {
				$this->sheets[ $this->sn ]['cellsInfo'][ $row + $this->_rowoffset ][ $col + $this->_coloffset ][ $key ] = $val;
			}
		}
	}

	/**
	 * [_GetIEEE754 description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $rknum [description]
	 * @return [type] [description]
	 */
	protected function _GetIEEE754( $rknum ) {
		if ( 0 !== ( $rknum & 0x02 ) ) {
			$value = $rknum >> 2;
		} else {
			// info on IEEE754 encoding from
			// http://research.microsoft.com/~hollasch/cgindex/coding/ieeefloat.html
			// The RK format calls for using only the most significant 30 bits of the
			// 64 bit floating point value. The other 34 bits are assumed to be 0
			// So, we use the upper 30 bits of $rknum as follows...
			$sign = ( $rknum & 0x80000000 ) >> 31;
			$exp = ( $rknum & 0x7ff00000 ) >> 20;
			$mantissa = ( 0x100000 | ( $rknum & 0x000ffffc ) );
			$value = $mantissa / pow( 2, ( 20 - ( $exp - 1023 ) ) );
			if ( $sign ) {
				$value *= -1;
			}
		}
		if ( 0 !== ( $rknum & 0x01 ) ) {
			$value /= 100;
		}
		return $value;
	}

	/**
	 * [_encodeUTF16 description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $string [description]
	 * @return [type] [description]
	 */
	protected function _encodeUTF16( $string ) {
		if ( $this->_defaultEncoding ) {
			switch ( $this->_encoderFunction ) {
				case 'iconv':
					$string = iconv( 'UTF-16LE', $this->_defaultEncoding, $string );
					break;
				case 'mb_convert_encoding':
					$string = mb_convert_encoding( $string, $this->_defaultEncoding, 'UTF-16LE' );
					break;
			}
		}
		return $string;
	}

	/**
	 * [_GetInt4d description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $data [description]
	 * @param [type] $pos  [description]
	 * @return [type] [description]
	 */
	protected function _GetInt4d( $data, $pos ) {
		$value = ord( $data[ $pos ] ) | ( ord( $data[ $pos + 1 ] ) << 8 ) | ( ord( $data[ $pos + 2 ] ) << 16 ) | ( ord( $data[ $pos + 3 ] ) << 24 );
		if ( $value >= 4294967294 ) {
			$value = -2;
		}
		return $value;
	}

	/**
	 * [v description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $data [description]
	 * @param [type] $pos  [description]
	 * @return [type] [description]
	 */
	protected function v( $data, $pos ) {
		return ord( $data[ $pos ] ) | ord( $data[ $pos + 1 ] ) << 8;
	}

} // class Spreadsheet_Excel_Reader
