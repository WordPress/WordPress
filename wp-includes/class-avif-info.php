<?php
/**
 * Copyright (c) 2021, Alliance for Open Media. All rights reserved
 *
 * This source code is subject to the terms of the BSD 2 Clause License and
 * the Alliance for Open Media Patent License 1.0. If the BSD 2 Clause License
 * was not distributed with this source code in the LICENSE file, you can
 * obtain it at www.aomedia.org/license/software. If the Alliance for Open
 * Media Patent License 1.0 was not distributed with this source code in the
 * PATENTS file, you can obtain it at www.aomedia.org/license/patent.
 *
 * Note: this class is from libavifinfo - https://aomedia.googlesource.com/libavifinfo/+/refs/heads/main/avifinfo.php at f509487.
 * It is used as a fallback to parse AVIF files when the server doesn't support AVIF,
 * primarily to identify the width and height of the image.
 *
 * Note PHP 8.2 added native support for AVIF, so this class can be removed when WordPress requires PHP 8.2.
 */

namespace Avifinfo;

const FOUND     = 0; // Input correctly parsed and information retrieved.
const NOT_FOUND = 1; // Input correctly parsed but information is missing or elsewhere.
const TRUNCATED = 2; // Input correctly parsed until missing bytes to continue.
const ABORTED   = 3; // Input correctly parsed until stopped to avoid timeout or crash.
const INVALID   = 4; // Input incorrectly parsed.

const MAX_SIZE      = 4294967295; // Unlikely to be insufficient to parse AVIF headers.
const MAX_NUM_BOXES = 4096;       // Be reasonable. Avoid timeouts and out-of-memory.
const MAX_VALUE     = 255;
const MAX_TILES     = 16;
const MAX_PROPS     = 32;
const MAX_FEATURES  = 8;
const UNDEFINED     = 0;          // Value was not yet parsed.

/**
 * Reads an unsigned integer with most significant bits first.
 *
 * @param binary string $input     Must be at least $num_bytes-long.
 * @param int           $num_bytes Number of parsed bytes.
 * @return int                     Value.
 */
function read_big_endian( $input, $num_bytes ) {
  if ( $num_bytes == 1 ) {
    return unpack( 'C', $input ) [1];
  } else if ( $num_bytes == 2 ) {
    return unpack( 'n', $input ) [1];
  } else if ( $num_bytes == 3 ) {
    $bytes = unpack( 'C3', $input );
    return ( $bytes[1] << 16 ) | ( $bytes[2] << 8 ) | $bytes[3];
  } else { // $num_bytes is 4
    // This might fail to read unsigned values >= 2^31 on 32-bit systems.
    // See https://www.php.net/manual/en/function.unpack.php#106041
    return unpack( 'N', $input ) [1];
  }
}

/**
 * Reads bytes and advances the stream position by the same count.
 *
 * @param stream               $handle    Bytes will be read from this resource.
 * @param int                  $num_bytes Number of bytes read. Must be greater than 0.
 * @return binary string|false            The raw bytes or false on failure.
 */
function read( $handle, $num_bytes ) {
  $data = fread( $handle, $num_bytes );
  return ( $data !== false && strlen( $data ) >= $num_bytes ) ? $data : false;
}

/**
 * Advances the stream position by the given offset.
 *
 * @param stream $handle    Bytes will be skipped from this resource.
 * @param int    $num_bytes Number of skipped bytes. Can be 0.
 * @return bool             True on success or false on failure.
 */
// Skips 'num_bytes' from the 'stream'. 'num_bytes' can be zero.
function skip( $handle, $num_bytes ) {
  return ( fseek( $handle, $num_bytes, SEEK_CUR ) == 0 );
}

//------------------------------------------------------------------------------
// Features are parsed into temporary property associations.

class Tile { // Tile item id <-> parent item id associations.
  public $tile_item_id;
  public $parent_item_id;
}

class Prop { // Property index <-> item id associations.
  public $property_index;
  public $item_id;
}

class Dim_Prop { // Property <-> features associations.
  public $property_index;
  public $width;
  public $height;
}

class Chan_Prop { // Property <-> features associations.
  public $property_index;
  public $bit_depth;
  public $num_channels;
}

class Features {
  public $has_primary_item = false; // True if "pitm" was parsed.
  public $has_alpha = false; // True if an alpha "auxC" was parsed.
  public $primary_item_id;
  public $primary_item_features = array( // Deduced from the data below.
    'width'        => UNDEFINED, // In number of pixels.
    'height'       => UNDEFINED, // Ignores mirror and rotation.
    'bit_depth'    => UNDEFINED, // Likely 8, 10 or 12 bits per channel per pixel.
    'num_channels' => UNDEFINED  // Likely 1, 2, 3 or 4 channels:
                                          //   (1 monochrome or 3 colors) + (0 or 1 alpha)
  );

  public $tiles = array(); // Tile[]
  public $props = array(); // Prop[]
  public $dim_props = array(); // Dim_Prop[]
  public $chan_props = array(); // Chan_Prop[]

  /**
   * Binds the width, height, bit depth and number of channels from stored internal features.
   *
   * @param int     $target_item_id Id of the item whose features will be bound.
   * @param int     $tile_depth     Maximum recursion to search within tile-parent relations.
   * @return Status                 FOUND on success or NOT_FOUND on failure.
   */
  private function get_item_features( $target_item_id, $tile_depth ) {
    foreach ( $this->props as $prop ) {
      if ( $prop->item_id != $target_item_id ) {
        continue;
      }

      // Retrieve the width and height of the primary item if not already done.
      if ( $target_item_id == $this->primary_item_id &&
           ( $this->primary_item_features['width'] == UNDEFINED ||
             $this->primary_item_features['height'] == UNDEFINED ) ) {
        foreach ( $this->dim_props as $dim_prop ) {
          if ( $dim_prop->property_index != $prop->property_index ) {
            continue;
          }
          $this->primary_item_features['width']  = $dim_prop->width;
          $this->primary_item_features['height'] = $dim_prop->height;
          if ( $this->primary_item_features['bit_depth'] != UNDEFINED &&
               $this->primary_item_features['num_channels'] != UNDEFINED ) {
            return FOUND;
          }
          break;
        }
      }
      // Retrieve the bit depth and number of channels of the target item if not
      // already done.
      if ( $this->primary_item_features['bit_depth'] == UNDEFINED ||
           $this->primary_item_features['num_channels'] == UNDEFINED ) {
        foreach ( $this->chan_props as $chan_prop ) {
          if ( $chan_prop->property_index != $prop->property_index ) {
            continue;
          }
          $this->primary_item_features['bit_depth']    = $chan_prop->bit_depth;
          $this->primary_item_features['num_channels'] = $chan_prop->num_channels;
          if ( $this->primary_item_features['width'] != UNDEFINED &&
              $this->primary_item_features['height'] != UNDEFINED ) {
            return FOUND;
          }
          break;
        }
      }
    }

    // Check for the bit_depth and num_channels in a tile if not yet found.
    if ( $tile_depth < 3 ) {
      foreach ( $this->tiles as $tile ) {
        if ( $tile->parent_item_id != $target_item_id ) {
          continue;
        }
        $status = $this->get_item_features( $tile->tile_item_id, $tile_depth + 1 );
        if ( $status != NOT_FOUND ) {
          return $status;
        }
      }
    }
    return NOT_FOUND;
  }

  /**
   * Finds the width, height, bit depth and number of channels of the primary item.
   *
   * @return Status FOUND on success or NOT_FOUND on failure.
   */
  public function get_primary_item_features() {
    // Nothing to do without the primary item ID.
    if ( !$this->has_primary_item ) {
      return NOT_FOUND;
    }
    // Early exit.
    if ( empty( $this->dim_props ) || empty( $this->chan_props ) ) {
      return NOT_FOUND;
    }
    $status = $this->get_item_features( $this->primary_item_id, /*tile_depth=*/ 0 );
    if ( $status != FOUND ) {
      return $status;
    }

    // "auxC" is parsed before the "ipma" properties so it is known now, if any.
    if ( $this->has_alpha ) {
      ++$this->primary_item_features['num_channels'];
    }
    return FOUND;
  }
}

//------------------------------------------------------------------------------

class Box {
  public $size; // In bytes.
  public $type; // Four characters.
  public $version; // 0 or actual version if this is a full box.
  public $flags; // 0 or actual value if this is a full box.
  public $content_size; // 'size' minus the header size.

  /**
   * Reads the box header.
   *
   * @param stream  $handle              The resource the header will be parsed from.
   * @param int     $num_parsed_boxes    The total number of parsed boxes. Prevents timeouts.
   * @param int     $num_remaining_bytes The number of bytes that should be available from the resource.
   * @return Status                      FOUND on success or an error on failure.
   */
  public function parse( $handle, &$num_parsed_boxes, $num_remaining_bytes = MAX_SIZE ) {
    // See ISO/IEC 14496-12:2012(E) 4.2
    $header_size = 8; // box 32b size + 32b type (at least)
    if ( $header_size > $num_remaining_bytes ) {
      return INVALID;
    }
    if ( !( $data = read( $handle, 8 ) ) ) {
      return TRUNCATED;
    }
    $this->size = read_big_endian( $data, 4 );
    $this->type = substr( $data, 4, 4 );
    // 'box->size==1' means 64-bit size should be read after the box type.
    // 'box->size==0' means this box extends to all remaining bytes.
    if ( $this->size == 1 ) {
      $header_size += 8;
      if ( $header_size > $num_remaining_bytes ) {
        return INVALID;
      }
      if ( !( $data = read( $handle, 8 ) ) ) {
        return TRUNCATED;
      }
      // Stop the parsing if any box has a size greater than 4GB.
      if ( read_big_endian( $data, 4 ) != 0 ) {
        return ABORTED;
      }
      // Read the 32 least-significant bits.
      $this->size = read_big_endian( substr( $data, 4, 4 ), 4 );
    } else if ( $this->size == 0 ) {
      $this->size = $num_remaining_bytes;
    }
    if ( $this->size < $header_size ) {
      return INVALID;
    }
    if ( $this->size > $num_remaining_bytes ) {
      return INVALID;
    }

    $has_fullbox_header = $this->type == 'meta' || $this->type == 'pitm' ||
                          $this->type == 'ipma' || $this->type == 'ispe' ||
                          $this->type == 'pixi' || $this->type == 'iref' ||
                          $this->type == 'auxC';
    if ( $has_fullbox_header ) {
      $header_size += 4;
    }
    if ( $this->size < $header_size ) {
      return INVALID;
    }
    $this->content_size = $this->size - $header_size;
    // Avoid timeouts. The maximum number of parsed boxes is arbitrary.
    ++$num_parsed_boxes;
    if ( $num_parsed_boxes >= MAX_NUM_BOXES ) {
      return ABORTED;
    }

    $this->version = 0;
    $this->flags   = 0;
    if ( $has_fullbox_header ) {
      if ( !( $data = read( $handle, 4 ) ) ) {
        return TRUNCATED;
      }
      $this->version = read_big_endian( $data, 1 );
      $this->flags   = read_big_endian( substr( $data, 1, 3 ), 3 );
      // See AV1 Image File Format (AVIF) 8.1
      // at https://aomediacodec.github.io/av1-avif/#avif-boxes (available when
      // https://github.com/AOMediaCodec/av1-avif/pull/170 is merged).
      $is_parsable = ( $this->type == 'meta' && $this->version <= 0 ) ||
                     ( $this->type == 'pitm' && $this->version <= 1 ) ||
                     ( $this->type == 'ipma' && $this->version <= 1 ) ||
                     ( $this->type == 'ispe' && $this->version <= 0 ) ||
                     ( $this->type == 'pixi' && $this->version <= 0 ) ||
                     ( $this->type == 'iref' && $this->version <= 1 ) ||
                     ( $this->type == 'auxC' && $this->version <= 0 );
      // Instead of considering this file as invalid, skip unparsable boxes.
      if ( !$is_parsable ) {
        $this->type = 'unknownversion';
      }
    }
    // print_r( $this ); // Uncomment to print all boxes.
    return FOUND;
  }
}

//------------------------------------------------------------------------------

class Parser {
  private $handle; // Input stream.
  private $num_parsed_boxes = 0;
  private $data_was_skipped = false;
  public $features;

  function __construct( $handle ) {
    $this->handle   = $handle;
    $this->features = new Features();
  }

  /**
   * Parses an "ipco" box.
   *
   * "ispe" is used for width and height, "pixi" and "av1C" are used for bit depth
   * and number of channels, and "auxC" is used for alpha.
   *
   * @param stream  $handle              The resource the box will be parsed from.
   * @param int     $num_remaining_bytes The number of bytes that should be available from the resource.
   * @return Status                      FOUND on success or an error on failure.
   */
  private function parse_ipco( $num_remaining_bytes ) {
    $box_index = 1; // 1-based index. Used for iterating over properties.
    do {
      $box    = new Box();
      $status = $box->parse( $this->handle, $this->num_parsed_boxes, $num_remaining_bytes );
      if ( $status != FOUND ) {
        return $status;
      }

      if ( $box->type == 'ispe' ) {
        // See ISO/IEC 23008-12:2017(E) 6.5.3.2
        if ( $box->content_size < 8 ) {
          return INVALID;
        }
        if ( !( $data = read( $this->handle, 8 ) ) ) {
          return TRUNCATED;
        }
        $width  = read_big_endian( substr( $data, 0, 4 ), 4 );
        $height = read_big_endian( substr( $data, 4, 4 ), 4 );
        if ( $width == 0 || $height == 0 ) {
          return INVALID;
        }
        if ( count( $this->features->dim_props ) <= MAX_FEATURES &&
             $box_index <= MAX_VALUE ) {
          $dim_prop_count = count( $this->features->dim_props );
          $this->features->dim_props[$dim_prop_count]                 = new Dim_Prop();
          $this->features->dim_props[$dim_prop_count]->property_index = $box_index;
          $this->features->dim_props[$dim_prop_count]->width          = $width;
          $this->features->dim_props[$dim_prop_count]->height         = $height;
        } else {
          $this->data_was_skipped = true;
        }
        if ( !skip( $this->handle, $box->content_size - 8 ) ) {
          return TRUNCATED;
        }
      } else if ( $box->type == 'pixi' ) {
        // See ISO/IEC 23008-12:2017(E) 6.5.6.2
        if ( $box->content_size < 1 ) {
          return INVALID;
        }
        if ( !( $data = read( $this->handle, 1 ) ) ) {
          return TRUNCATED;
        }
        $num_channels = read_big_endian( $data, 1 );
        if ( $num_channels < 1 ) {
          return INVALID;
        }
        if ( $box->content_size < 1 + $num_channels ) {
          return INVALID;
        }
        if ( !( $data = read( $this->handle, 1 ) ) ) {
          return TRUNCATED;
        }
        $bit_depth = read_big_endian( $data, 1 );
        if ( $bit_depth < 1 ) {
          return INVALID;
        }
        for ( $i = 1; $i < $num_channels; ++$i ) {
          if ( !( $data = read( $this->handle, 1 ) ) ) {
            return TRUNCATED;
          }
          // Bit depth should be the same for all channels.
          if ( read_big_endian( $data, 1 ) != $bit_depth ) {
            return INVALID;
          }
          if ( $i > 32 ) {
            return ABORTED; // Be reasonable.
          }
        }
        if ( count( $this->features->chan_props ) <= MAX_FEATURES &&
             $box_index <= MAX_VALUE && $bit_depth <= MAX_VALUE &&
             $num_channels <= MAX_VALUE ) {
          $chan_prop_count = count( $this->features->chan_props );
          $this->features->chan_props[$chan_prop_count]                 = new Chan_Prop();
          $this->features->chan_props[$chan_prop_count]->property_index = $box_index;
          $this->features->chan_props[$chan_prop_count]->bit_depth      = $bit_depth;
          $this->features->chan_props[$chan_prop_count]->num_channels   = $num_channels;
        } else {
          $this->data_was_skipped = true;
        }
        if ( !skip( $this->handle, $box->content_size - ( 1 + $num_channels ) ) ) {
          return TRUNCATED;
        }
      } else if ( $box->type == 'av1C' ) {
        // See AV1 Codec ISO Media File Format Binding 2.3.1
        // at https://aomediacodec.github.io/av1-isobmff/#av1c
        // Only parse the necessary third byte. Assume that the others are valid.
        if ( $box->content_size < 3 ) {
          return INVALID;
        }
        if ( !( $data = read( $this->handle, 3 ) ) ) {
          return TRUNCATED;
        }
        $byte          = read_big_endian( substr( $data, 2, 1 ), 1 );
        $high_bitdepth = ( $byte & 0x40 ) != 0;
        $twelve_bit    = ( $byte & 0x20 ) != 0;
        $monochrome    = ( $byte & 0x10 ) != 0;
        if ( $twelve_bit && !$high_bitdepth ) {
            return INVALID;
        }
        if ( count( $this->features->chan_props ) <= MAX_FEATURES &&
             $box_index <= MAX_VALUE ) {
          $chan_prop_count = count( $this->features->chan_props );
          $this->features->chan_props[$chan_prop_count]                 = new Chan_Prop();
          $this->features->chan_props[$chan_prop_count]->property_index = $box_index;
          $this->features->chan_props[$chan_prop_count]->bit_depth      =
              $high_bitdepth ? $twelve_bit ? 12 : 10 : 8;
          $this->features->chan_props[$chan_prop_count]->num_channels   = $monochrome ? 1 : 3;
        } else {
          $this->data_was_skipped = true;
        }
        if ( !skip( $this->handle, $box->content_size - 3 ) ) {
          return TRUNCATED;
        }
      } else if ( $box->type == 'auxC' ) {
        // See AV1 Image File Format (AVIF) 4
        // at https://aomediacodec.github.io/av1-avif/#auxiliary-images
        $kAlphaStr       = "urn:mpeg:mpegB:cicp:systems:auxiliary:alpha\0";
        $kAlphaStrLength = 44; // Includes terminating character.
        if ( $box->content_size >= $kAlphaStrLength ) {
          if ( !( $data = read( $this->handle, $kAlphaStrLength ) ) ) {
            return TRUNCATED;
          }
          if ( substr( $data, 0, $kAlphaStrLength ) == $kAlphaStr ) {
            // Note: It is unlikely but it is possible that this alpha plane does
            //       not belong to the primary item or a tile. Ignore this issue.
            $this->features->has_alpha = true;
          }
          if ( !skip( $this->handle, $box->content_size - $kAlphaStrLength ) ) {
            return TRUNCATED;
          }
        } else {
          if ( !skip( $this->handle, $box->content_size ) ) {
            return TRUNCATED;
          }
        }
      } else {
        if ( !skip( $this->handle, $box->content_size ) ) {
          return TRUNCATED;
        }
      }
      ++$box_index;
      $num_remaining_bytes -= $box->size;
    } while ( $num_remaining_bytes > 0 );
    return NOT_FOUND;
  }

  /**
   * Parses an "iprp" box.
   *
   * The "ipco" box contain the properties which are linked to items by the "ipma" box.
   *
   * @param stream  $handle              The resource the box will be parsed from.
   * @param int     $num_remaining_bytes The number of bytes that should be available from the resource.
   * @return Status                      FOUND on success or an error on failure.
   */
  private function parse_iprp( $num_remaining_bytes ) {
    do {
      $box    = new Box();
      $status = $box->parse( $this->handle, $this->num_parsed_boxes, $num_remaining_bytes );
      if ( $status != FOUND ) {
        return $status;
      }

      if ( $box->type == 'ipco' ) {
        $status = $this->parse_ipco( $box->content_size );
        if ( $status != NOT_FOUND ) {
          return $status;
        }
      } else if ( $box->type == 'ipma' ) {
        // See ISO/IEC 23008-12:2017(E) 9.3.2
        $num_read_bytes = 4;
        if ( $box->content_size < $num_read_bytes ) {
          return INVALID;
        }
        if ( !( $data = read( $this->handle, $num_read_bytes ) ) ) {
          return TRUNCATED;
        }
        $entry_count        = read_big_endian( $data, 4 );
        $id_num_bytes       = ( $box->version < 1 ) ? 2 : 4;
        $index_num_bytes    = ( $box->flags & 1 ) ? 2 : 1;
        $essential_bit_mask = ( $box->flags & 1 ) ? 0x8000 : 0x80;

        for ( $entry = 0; $entry < $entry_count; ++$entry ) {
          if ( $entry >= MAX_PROPS ||
               count( $this->features->props ) >= MAX_PROPS ) {
            $this->data_was_skipped = true;
            break;
          }
          $num_read_bytes += $id_num_bytes + 1;
          if ( $box->content_size < $num_read_bytes ) {
            return INVALID;
          }
          if ( !( $data = read( $this->handle, $id_num_bytes + 1 ) ) ) {
            return TRUNCATED;
          }
          $item_id           = read_big_endian(
              substr( $data, 0, $id_num_bytes ), $id_num_bytes );
          $association_count = read_big_endian(
              substr( $data, $id_num_bytes, 1 ), 1 );

          for ( $property = 0; $property < $association_count; ++$property ) {
            if ( $property >= MAX_PROPS ||
                 count( $this->features->props ) >= MAX_PROPS ) {
              $this->data_was_skipped = true;
              break;
            }
            $num_read_bytes += $index_num_bytes;
            if ( $box->content_size < $num_read_bytes ) {
              return INVALID;
            }
            if ( !( $data = read( $this->handle, $index_num_bytes ) ) ) {
              return TRUNCATED;
            }
            $value          = read_big_endian( $data, $index_num_bytes );
            // $essential = ($value & $essential_bit_mask);  // Unused.
            $property_index = ( $value & ~$essential_bit_mask );
            if ( $property_index <= MAX_VALUE && $item_id <= MAX_VALUE ) {
              $prop_count = count( $this->features->props );
              $this->features->props[$prop_count]                 = new Prop();
              $this->features->props[$prop_count]->property_index = $property_index;
              $this->features->props[$prop_count]->item_id        = $item_id;
            } else {
              $this->data_was_skipped = true;
            }
          }
          if ( $property < $association_count ) {
            break; // Do not read garbage.
          }
        }

        // If all features are available now, do not look further.
        $status = $this->features->get_primary_item_features();
        if ( $status != NOT_FOUND ) {
          return $status;
        }

        // Mostly if 'data_was_skipped'.
        if ( !skip( $this->handle, $box->content_size - $num_read_bytes ) ) {
          return TRUNCATED;
        }
      } else {
        if ( !skip( $this->handle, $box->content_size ) ) {
          return TRUNCATED;
        }
      }
      $num_remaining_bytes -= $box->size;
    } while ( $num_remaining_bytes > 0 );
    return NOT_FOUND;
  }

  /**
   * Parses an "iref" box.
   *
   * The "dimg" boxes contain links between tiles and their parent items, which
   * can be used to infer bit depth and number of channels for the primary item
   * when the latter does not have these properties.
   *
   * @param stream  $handle              The resource the box will be parsed from.
   * @param int     $num_remaining_bytes The number of bytes that should be available from the resource.
   * @return Status                      FOUND on success or an error on failure.
   */
  private function parse_iref( $num_remaining_bytes ) {
    do {
      $box    = new Box();
      $status = $box->parse( $this->handle, $this->num_parsed_boxes, $num_remaining_bytes );
      if ( $status != FOUND ) {
        return $status;
      }

      if ( $box->type == 'dimg' ) {
        // See ISO/IEC 14496-12:2015(E) 8.11.12.2
        $num_bytes_per_id = ( $box->version == 0 ) ? 2 : 4;
        $num_read_bytes   = $num_bytes_per_id + 2;
        if ( $box->content_size < $num_read_bytes ) {
          return INVALID;
        }
        if ( !( $data = read( $this->handle, $num_read_bytes ) ) ) {
          return TRUNCATED;
        }
        $from_item_id    = read_big_endian( $data, $num_bytes_per_id );
        $reference_count = read_big_endian( substr( $data, $num_bytes_per_id, 2 ), 2 );

        for ( $i = 0; $i < $reference_count; ++$i ) {
          if ( $i >= MAX_TILES ) {
            $this->data_was_skipped = true;
            break;
          }
          $num_read_bytes += $num_bytes_per_id;
          if ( $box->content_size < $num_read_bytes ) {
            return INVALID;
          }
          if ( !( $data = read( $this->handle, $num_bytes_per_id ) ) ) {
            return TRUNCATED;
          }
          $to_item_id = read_big_endian( $data, $num_bytes_per_id );
          $tile_count = count( $this->features->tiles );
          if ( $from_item_id <= MAX_VALUE && $to_item_id <= MAX_VALUE &&
               $tile_count < MAX_TILES ) {
            $this->features->tiles[$tile_count]                 = new Tile();
            $this->features->tiles[$tile_count]->tile_item_id   = $to_item_id;
            $this->features->tiles[$tile_count]->parent_item_id = $from_item_id;
          } else {
            $this->data_was_skipped = true;
          }
        }

        // If all features are available now, do not look further.
        $status = $this->features->get_primary_item_features();
        if ( $status != NOT_FOUND ) {
          return $status;
        }

        // Mostly if 'data_was_skipped'.
        if ( !skip( $this->handle, $box->content_size - $num_read_bytes ) ) {
          return TRUNCATED;
        }
      } else {
        if ( !skip( $this->handle, $box->content_size ) ) {
          return TRUNCATED;
        }
      }
      $num_remaining_bytes -= $box->size;
    } while ( $num_remaining_bytes > 0 );
    return NOT_FOUND;
  }

  /**
   * Parses a "meta" box.
   *
   * It looks for the primary item ID in the "pitm" box and recurses into other boxes
   * to find its features.
   *
   * @param stream  $handle              The resource the box will be parsed from.
   * @param int     $num_remaining_bytes The number of bytes that should be available from the resource.
   * @return Status                      FOUND on success or an error on failure.
   */
  private function parse_meta( $num_remaining_bytes ) {
    do {
      $box    = new Box();
      $status = $box->parse( $this->handle, $this->num_parsed_boxes, $num_remaining_bytes );
      if ( $status != FOUND ) {
        return $status;
      }

      if ( $box->type == 'pitm' ) {
        // See ISO/IEC 14496-12:2015(E) 8.11.4.2
        $num_bytes_per_id = ( $box->version == 0 ) ? 2 : 4;
        if ( $num_bytes_per_id > $num_remaining_bytes ) {
          return INVALID;
        }
        if ( !( $data = read( $this->handle, $num_bytes_per_id ) ) ) {
          return TRUNCATED;
        }
        $primary_item_id = read_big_endian( $data, $num_bytes_per_id );
        if ( $primary_item_id > MAX_VALUE ) {
          return ABORTED;
        }
        $this->features->has_primary_item = true;
        $this->features->primary_item_id  = $primary_item_id;
        if ( !skip( $this->handle, $box->content_size - $num_bytes_per_id ) ) {
          return TRUNCATED;
        }
      } else if ( $box->type == 'iprp' ) {
        $status = $this->parse_iprp( $box->content_size );
        if ( $status != NOT_FOUND ) {
          return $status;
        }
      } else if ( $box->type == 'iref' ) {
        $status = $this->parse_iref( $box->content_size );
        if ( $status != NOT_FOUND ) {
          return $status;
        }
      } else {
        if ( !skip( $this->handle, $box->content_size ) ) {
          return TRUNCATED;
        }
      }
      $num_remaining_bytes -= $box->size;
    } while ( $num_remaining_bytes != 0 );
    // According to ISO/IEC 14496-12:2012(E) 8.11.1.1 there is at most one "meta".
    return INVALID;
  }

  /**
   * Parses a file stream.
   *
   * The file type is checked through the "ftyp" box.
   *
   * @return bool True if the input stream is an AVIF bitstream or false.
   */
  public function parse_ftyp() {
    $box    = new Box();
    $status = $box->parse( $this->handle, $this->num_parsed_boxes );
    if ( $status != FOUND ) {
      return false;
    }

    if ( $box->type != 'ftyp' ) {
      return false;
    }
    // Iterate over brands. See ISO/IEC 14496-12:2012(E) 4.3.1
    if ( $box->content_size < 8 ) {
      return false;
    }
    for ( $i = 0; $i + 4 <= $box->content_size; $i += 4 ) {
      if ( !( $data = read( $this->handle, 4 ) ) ) {
        return false;
      }
      if ( $i == 4 ) {
        continue; // Skip minor_version.
      }
      if ( substr( $data, 0, 4 ) == 'avif' || substr( $data, 0, 4 ) == 'avis' ) {
        return skip( $this->handle, $box->content_size - ( $i + 4 ) );
      }
      if ( $i > 32 * 4 ) {
        return false; // Be reasonable.
      }

    }
    return false; // No AVIF brand no good.
  }

  /**
   * Parses a file stream.
   *
   * Features are extracted from the "meta" box.
   *
   * @return bool True if the main features of the primary item were parsed or false.
   */
  public function parse_file() {
    $box = new Box();
    while ( $box->parse( $this->handle, $this->num_parsed_boxes ) == FOUND ) {
      if ( $box->type === 'meta' ) {
        if ( $this->parse_meta( $box->content_size ) != FOUND ) {
          return false;
        }
        return true;
      }
      if ( !skip( $this->handle, $box->content_size ) ) {
        return false;
      }
    }
    return false; // No "meta" no good.
  }
}
