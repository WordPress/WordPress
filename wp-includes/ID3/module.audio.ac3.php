<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
//          also https://github.com/JamesHeinrich/getID3       //
/////////////////////////////////////////////////////////////////
// See readme.txt for more details                             //
/////////////////////////////////////////////////////////////////
//                                                             //
// module.audio.ac3.php                                        //
// module for analyzing AC-3 (aka Dolby Digital) audio files   //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_ac3 extends getid3_handler
{
    private $AC3header = array();
    private $BSIoffset = 0;

    const syncword = 0x0B77;

	public function Analyze() {
		$info = &$this->getid3->info;

		///AH
		$info['ac3']['raw']['bsi'] = array();
		$thisfile_ac3              = &$info['ac3'];
		$thisfile_ac3_raw          = &$thisfile_ac3['raw'];
		$thisfile_ac3_raw_bsi      = &$thisfile_ac3_raw['bsi'];


		// http://www.atsc.org/standards/a_52a.pdf

		$info['fileformat'] = 'ac3';

		// An AC-3 serial coded audio bit stream is made up of a sequence of synchronization frames
		// Each synchronization frame contains 6 coded audio blocks (AB), each of which represent 256
		// new audio samples per channel. A synchronization information (SI) header at the beginning
		// of each frame contains information needed to acquire and maintain synchronization. A
		// bit stream information (BSI) header follows SI, and contains parameters describing the coded
		// audio service. The coded audio blocks may be followed by an auxiliary data (Aux) field. At the
		// end of each frame is an error check field that includes a CRC word for error detection. An
		// additional CRC word is located in the SI header, the use of which, by a decoder, is optional.
		//
		// syncinfo() | bsi() | AB0 | AB1 | AB2 | AB3 | AB4 | AB5 | Aux | CRC

		// syncinfo() {
		// 	 syncword    16
		// 	 crc1        16
		// 	 fscod        2
		// 	 frmsizecod   6
		// } /* end of syncinfo */

		$this->fseek($info['avdataoffset']);
		$tempAC3header = $this->fread(100); // should be enough to cover all data, there are some variable-length fields...?
		$this->AC3header['syncinfo']  =     getid3_lib::BigEndian2Int(substr($tempAC3header, 0, 2));
		$this->AC3header['bsi']       =     getid3_lib::BigEndian2Bin(substr($tempAC3header, 2));
		$thisfile_ac3_raw_bsi['bsid'] = (getid3_lib::LittleEndian2Int(substr($tempAC3header, 5, 1)) & 0xF8) >> 3; // AC3 and E-AC3 put the "bsid" version identifier in the same place, but unfortnately the 4 bytes between the syncword and the version identifier are interpreted differently, so grab it here so the following code structure can make sense
		unset($tempAC3header);

		if ($this->AC3header['syncinfo'] !== self::syncword) {
			if (!$this->isDependencyFor('matroska')) {
				unset($info['fileformat'], $info['ac3']);
				return $this->error('Expecting "'.dechex(self::syncword).'" at offset '.$info['avdataoffset'].', found "'.dechex($this->AC3header['syncinfo']).'"');
			}
		}

		$info['audio']['dataformat']   = 'ac3';
		$info['audio']['bitrate_mode'] = 'cbr';
		$info['audio']['lossless']     = false;

		if ($thisfile_ac3_raw_bsi['bsid'] <= 8) {

			$thisfile_ac3_raw_bsi['crc1']       = getid3_lib::Bin2Dec($this->readHeaderBSI(16));
			$thisfile_ac3_raw_bsi['fscod']      =                     $this->readHeaderBSI(2);   // 5.4.1.3
			$thisfile_ac3_raw_bsi['frmsizecod'] =                     $this->readHeaderBSI(6);   // 5.4.1.4
			if ($thisfile_ac3_raw_bsi['frmsizecod'] > 37) { // binary: 100101 - see Table 5.18 Frame Size Code Table (1 word = 16 bits)
				$this->warning('Unexpected ac3.bsi.frmsizecod value: '.$thisfile_ac3_raw_bsi['frmsizecod'].', bitrate not set correctly');
			}

			$thisfile_ac3_raw_bsi['bsid']  = $this->readHeaderBSI(5); // we already know this from pre-parsing the version identifier, but re-read it to let the bitstream flow as intended
			$thisfile_ac3_raw_bsi['bsmod'] = $this->readHeaderBSI(3);
			$thisfile_ac3_raw_bsi['acmod'] = $this->readHeaderBSI(3);

			if ($thisfile_ac3_raw_bsi['acmod'] & 0x01) {
				// If the lsb of acmod is a 1, center channel is in use and cmixlev follows in the bit stream.
				$thisfile_ac3_raw_bsi['cmixlev'] = $this->readHeaderBSI(2);
				$thisfile_ac3['center_mix_level'] = self::centerMixLevelLookup($thisfile_ac3_raw_bsi['cmixlev']);
			}

			if ($thisfile_ac3_raw_bsi['acmod'] & 0x04) {
				// If the msb of acmod is a 1, surround channels are in use and surmixlev follows in the bit stream.
				$thisfile_ac3_raw_bsi['surmixlev'] = $this->readHeaderBSI(2);
				$thisfile_ac3['surround_mix_level'] = self::surroundMixLevelLookup($thisfile_ac3_raw_bsi['surmixlev']);
			}

			if ($thisfile_ac3_raw_bsi['acmod'] == 0x02) {
				// When operating in the two channel mode, this 2-bit code indicates whether or not the program has been encoded in Dolby Surround.
				$thisfile_ac3_raw_bsi['dsurmod'] = $this->readHeaderBSI(2);
				$thisfile_ac3['dolby_surround_mode'] = self::dolbySurroundModeLookup($thisfile_ac3_raw_bsi['dsurmod']);
			}

			$thisfile_ac3_raw_bsi['flags']['lfeon'] = (bool) $this->readHeaderBSI(1);

			// This indicates how far the average dialogue level is below digital 100 percent. Valid values are 1-31.
			// The value of 0 is reserved. The values of 1 to 31 are interpreted as -1 dB to -31 dB with respect to digital 100 percent.
			$thisfile_ac3_raw_bsi['dialnorm'] = $this->readHeaderBSI(5);                 // 5.4.2.8 dialnorm: Dialogue Normalization, 5 Bits

			$thisfile_ac3_raw_bsi['flags']['compr'] = (bool) $this->readHeaderBSI(1);       // 5.4.2.9 compre: Compression Gain Word Exists, 1 Bit
			if ($thisfile_ac3_raw_bsi['flags']['compr']) {
				$thisfile_ac3_raw_bsi['compr'] = $this->readHeaderBSI(8);                // 5.4.2.10 compr: Compression Gain Word, 8 Bits
				$thisfile_ac3['heavy_compression'] = self::heavyCompression($thisfile_ac3_raw_bsi['compr']);
			}

			$thisfile_ac3_raw_bsi['flags']['langcod'] = (bool) $this->readHeaderBSI(1);     // 5.4.2.11 langcode: Language Code Exists, 1 Bit
			if ($thisfile_ac3_raw_bsi['flags']['langcod']) {
				$thisfile_ac3_raw_bsi['langcod'] = $this->readHeaderBSI(8);              // 5.4.2.12 langcod: Language Code, 8 Bits
			}

			$thisfile_ac3_raw_bsi['flags']['audprodinfo'] = (bool) $this->readHeaderBSI(1);  // 5.4.2.13 audprodie: Audio Production Information Exists, 1 Bit
			if ($thisfile_ac3_raw_bsi['flags']['audprodinfo']) {
				$thisfile_ac3_raw_bsi['mixlevel'] = $this->readHeaderBSI(5);             // 5.4.2.14 mixlevel: Mixing Level, 5 Bits
				$thisfile_ac3_raw_bsi['roomtyp']  = $this->readHeaderBSI(2);             // 5.4.2.15 roomtyp: Room Type, 2 Bits

				$thisfile_ac3['mixing_level'] = (80 + $thisfile_ac3_raw_bsi['mixlevel']).'dB';
				$thisfile_ac3['room_type']    = self::roomTypeLookup($thisfile_ac3_raw_bsi['roomtyp']);
			}


			$thisfile_ac3_raw_bsi['dialnorm2'] = $this->readHeaderBSI(5);                // 5.4.2.16 dialnorm2: Dialogue Normalization, ch2, 5 Bits
			$thisfile_ac3['dialogue_normalization2'] = '-'.$thisfile_ac3_raw_bsi['dialnorm2'].'dB';  // This indicates how far the average dialogue level is below digital 100 percent. Valid values are 1-31. The value of 0 is reserved. The values of 1 to 31 are interpreted as -1 dB to -31 dB with respect to digital 100 percent.

			$thisfile_ac3_raw_bsi['flags']['compr2'] = (bool) $this->readHeaderBSI(1);       // 5.4.2.17 compr2e: Compression Gain Word Exists, ch2, 1 Bit
			if ($thisfile_ac3_raw_bsi['flags']['compr2']) {
				$thisfile_ac3_raw_bsi['compr2'] = $this->readHeaderBSI(8);               // 5.4.2.18 compr2: Compression Gain Word, ch2, 8 Bits
				$thisfile_ac3['heavy_compression2'] = self::heavyCompression($thisfile_ac3_raw_bsi['compr2']);
			}

			$thisfile_ac3_raw_bsi['flags']['langcod2'] = (bool) $this->readHeaderBSI(1);    // 5.4.2.19 langcod2e: Language Code Exists, ch2, 1 Bit
			if ($thisfile_ac3_raw_bsi['flags']['langcod2']) {
				$thisfile_ac3_raw_bsi['langcod2'] = $this->readHeaderBSI(8);             // 5.4.2.20 langcod2: Language Code, ch2, 8 Bits
			}

			$thisfile_ac3_raw_bsi['flags']['audprodinfo2'] = (bool) $this->readHeaderBSI(1); // 5.4.2.21 audprodi2e: Audio Production Information Exists, ch2, 1 Bit
			if ($thisfile_ac3_raw_bsi['flags']['audprodinfo2']) {
				$thisfile_ac3_raw_bsi['mixlevel2'] = $this->readHeaderBSI(5);            // 5.4.2.22 mixlevel2: Mixing Level, ch2, 5 Bits
				$thisfile_ac3_raw_bsi['roomtyp2']  = $this->readHeaderBSI(2);            // 5.4.2.23 roomtyp2: Room Type, ch2, 2 Bits

				$thisfile_ac3['mixing_level2'] = (80 + $thisfile_ac3_raw_bsi['mixlevel2']).'dB';
				$thisfile_ac3['room_type2']    = self::roomTypeLookup($thisfile_ac3_raw_bsi['roomtyp2']);
			}

			$thisfile_ac3_raw_bsi['copyright'] = (bool) $this->readHeaderBSI(1);         // 5.4.2.24 copyrightb: Copyright Bit, 1 Bit

			$thisfile_ac3_raw_bsi['original']  = (bool) $this->readHeaderBSI(1);         // 5.4.2.25 origbs: Original Bit Stream, 1 Bit

			$thisfile_ac3_raw_bsi['flags']['timecod1'] = $this->readHeaderBSI(2);            // 5.4.2.26 timecod1e, timcode2e: Time Code (first and second) Halves Exist, 2 Bits
			if ($thisfile_ac3_raw_bsi['flags']['timecod1'] & 0x01) {
				$thisfile_ac3_raw_bsi['timecod1'] = $this->readHeaderBSI(14);            // 5.4.2.27 timecod1: Time code first half, 14 bits
				$thisfile_ac3['timecode1'] = 0;
				$thisfile_ac3['timecode1'] += (($thisfile_ac3_raw_bsi['timecod1'] & 0x3E00) >>  9) * 3600;  // The first 5 bits of this 14-bit field represent the time in hours, with valid values of 0�23
				$thisfile_ac3['timecode1'] += (($thisfile_ac3_raw_bsi['timecod1'] & 0x01F8) >>  3) *   60;  // The next 6 bits represent the time in minutes, with valid values of 0�59
				$thisfile_ac3['timecode1'] += (($thisfile_ac3_raw_bsi['timecod1'] & 0x0003) >>  0) *    8;  // The final 3 bits represents the time in 8 second increments, with valid values of 0�7 (representing 0, 8, 16, ... 56 seconds)
			}
			if ($thisfile_ac3_raw_bsi['flags']['timecod1'] & 0x02) {
				$thisfile_ac3_raw_bsi['timecod2'] = $this->readHeaderBSI(14);            // 5.4.2.28 timecod2: Time code second half, 14 bits
				$thisfile_ac3['timecode2'] = 0;
				$thisfile_ac3['timecode2'] += (($thisfile_ac3_raw_bsi['timecod2'] & 0x3800) >> 11) *   1;              // The first 3 bits of this 14-bit field represent the time in seconds, with valid values from 0�7 (representing 0-7 seconds)
				$thisfile_ac3['timecode2'] += (($thisfile_ac3_raw_bsi['timecod2'] & 0x07C0) >>  6) *  (1 / 30);        // The next 5 bits represents the time in frames, with valid values from 0�29 (one frame = 1/30th of a second)
				$thisfile_ac3['timecode2'] += (($thisfile_ac3_raw_bsi['timecod2'] & 0x003F) >>  0) * ((1 / 30) / 60);  // The final 6 bits represents fractions of 1/64 of a frame, with valid values from 0�63
			}

			$thisfile_ac3_raw_bsi['flags']['addbsi'] = (bool) $this->readHeaderBSI(1);
			if ($thisfile_ac3_raw_bsi['flags']['addbsi']) {
				$thisfile_ac3_raw_bsi['addbsi_length'] = $this->readHeaderBSI(6) + 1; // This 6-bit code, which exists only if addbside is a 1, indicates the length in bytes of additional bit stream information. The valid range of addbsil is 0�63, indicating 1�64 additional bytes, respectively.

				$this->AC3header['bsi'] .= getid3_lib::BigEndian2Bin($this->fread($thisfile_ac3_raw_bsi['addbsi_length']));

				$thisfile_ac3_raw_bsi['addbsi_data'] = substr($this->AC3header['bsi'], $this->BSIoffset, $thisfile_ac3_raw_bsi['addbsi_length'] * 8);
				$this->BSIoffset += $thisfile_ac3_raw_bsi['addbsi_length'] * 8;
			}


		} elseif ($thisfile_ac3_raw_bsi['bsid'] <= 16) { // E-AC3


$this->error('E-AC3 parsing is incomplete and experimental in this version of getID3 ('.$this->getid3->version().'). Notably the bitrate calculations are wrong -- value might (or not) be correct, but it is not calculated correctly. Email info@getid3.org if you know how to calculate EAC3 bitrate correctly.');
			$info['audio']['dataformat'] = 'eac3';

			$thisfile_ac3_raw_bsi['strmtyp']          =        $this->readHeaderBSI(2);
			$thisfile_ac3_raw_bsi['substreamid']      =        $this->readHeaderBSI(3);
			$thisfile_ac3_raw_bsi['frmsiz']           =        $this->readHeaderBSI(11);
			$thisfile_ac3_raw_bsi['fscod']            =        $this->readHeaderBSI(2);
			if ($thisfile_ac3_raw_bsi['fscod'] == 3) {
				$thisfile_ac3_raw_bsi['fscod2']       =        $this->readHeaderBSI(2);
				$thisfile_ac3_raw_bsi['numblkscod'] = 3; // six blocks per syncframe
			} else {
				$thisfile_ac3_raw_bsi['numblkscod']   =        $this->readHeaderBSI(2);
			}
			$thisfile_ac3['bsi']['blocks_per_sync_frame'] = self::blocksPerSyncFrame($thisfile_ac3_raw_bsi['numblkscod']);
			$thisfile_ac3_raw_bsi['acmod']            =        $this->readHeaderBSI(3);
			$thisfile_ac3_raw_bsi['flags']['lfeon']   = (bool) $this->readHeaderBSI(1);
			$thisfile_ac3_raw_bsi['bsid']             =        $this->readHeaderBSI(5); // we already know this from pre-parsing the version identifier, but re-read it to let the bitstream flow as intended
			$thisfile_ac3_raw_bsi['dialnorm']         =        $this->readHeaderBSI(5);
			$thisfile_ac3_raw_bsi['flags']['compr']       = (bool) $this->readHeaderBSI(1);
			if ($thisfile_ac3_raw_bsi['flags']['compr']) {
				$thisfile_ac3_raw_bsi['compr']        =        $this->readHeaderBSI(8);
			}
			if ($thisfile_ac3_raw_bsi['acmod'] == 0) { // if 1+1 mode (dual mono, so some items need a second value)
				$thisfile_ac3_raw_bsi['dialnorm2']    =        $this->readHeaderBSI(5);
				$thisfile_ac3_raw_bsi['flags']['compr2']  = (bool) $this->readHeaderBSI(1);
				if ($thisfile_ac3_raw_bsi['flags']['compr2']) {
					$thisfile_ac3_raw_bsi['compr2']   =        $this->readHeaderBSI(8);
				}
			}
			if ($thisfile_ac3_raw_bsi['strmtyp'] == 1) { // if dependent stream
				$thisfile_ac3_raw_bsi['flags']['chanmap'] = (bool) $this->readHeaderBSI(1);
				if ($thisfile_ac3_raw_bsi['flags']['chanmap']) {
					$thisfile_ac3_raw_bsi['chanmap']  =        $this->readHeaderBSI(8);
				}
			}
			$thisfile_ac3_raw_bsi['flags']['mixmdat']     = (bool) $this->readHeaderBSI(1);
			if ($thisfile_ac3_raw_bsi['flags']['mixmdat']) { // Mixing metadata
				if ($thisfile_ac3_raw_bsi['acmod'] > 2) { // if more than 2 channels
					$thisfile_ac3_raw_bsi['dmixmod']  =        $this->readHeaderBSI(2);
				}
				if (($thisfile_ac3_raw_bsi['acmod'] & 0x01) && ($thisfile_ac3_raw_bsi['acmod'] > 2)) { // if three front channels exist
					$thisfile_ac3_raw_bsi['ltrtcmixlev'] =        $this->readHeaderBSI(3);
					$thisfile_ac3_raw_bsi['lorocmixlev'] =        $this->readHeaderBSI(3);
				}
				if ($thisfile_ac3_raw_bsi['acmod'] & 0x04) { // if a surround channel exists
					$thisfile_ac3_raw_bsi['ltrtsurmixlev'] =        $this->readHeaderBSI(3);
					$thisfile_ac3_raw_bsi['lorosurmixlev'] =        $this->readHeaderBSI(3);
				}
				if ($thisfile_ac3_raw_bsi['flags']['lfeon']) { // if the LFE channel exists
					$thisfile_ac3_raw_bsi['flags']['lfemixlevcod'] = (bool) $this->readHeaderBSI(1);
					if ($thisfile_ac3_raw_bsi['flags']['lfemixlevcod']) {
						$thisfile_ac3_raw_bsi['lfemixlevcod']  =        $this->readHeaderBSI(5);
					}
				}
				if ($thisfile_ac3_raw_bsi['strmtyp'] == 0) { // if independent stream
					$thisfile_ac3_raw_bsi['flags']['pgmscl'] = (bool) $this->readHeaderBSI(1);
					if ($thisfile_ac3_raw_bsi['flags']['pgmscl']) {
						$thisfile_ac3_raw_bsi['pgmscl']  =        $this->readHeaderBSI(6);
					}
					if ($thisfile_ac3_raw_bsi['acmod'] == 0) { // if 1+1 mode (dual mono, so some items need a second value)
						$thisfile_ac3_raw_bsi['flags']['pgmscl2'] = (bool) $this->readHeaderBSI(1);
						if ($thisfile_ac3_raw_bsi['flags']['pgmscl2']) {
							$thisfile_ac3_raw_bsi['pgmscl2']  =        $this->readHeaderBSI(6);
						}
					}
					$thisfile_ac3_raw_bsi['flags']['extpgmscl'] = (bool) $this->readHeaderBSI(1);
					if ($thisfile_ac3_raw_bsi['flags']['extpgmscl']) {
						$thisfile_ac3_raw_bsi['extpgmscl']  =        $this->readHeaderBSI(6);
					}
					$thisfile_ac3_raw_bsi['mixdef']  =        $this->readHeaderBSI(2);
					if ($thisfile_ac3_raw_bsi['mixdef'] == 1) { // mixing option 2
						$thisfile_ac3_raw_bsi['premixcmpsel']  = (bool) $this->readHeaderBSI(1);
						$thisfile_ac3_raw_bsi['drcsrc']        = (bool) $this->readHeaderBSI(1);
						$thisfile_ac3_raw_bsi['premixcmpscl']  =        $this->readHeaderBSI(3);
					} elseif ($thisfile_ac3_raw_bsi['mixdef'] == 2) { // mixing option 3
						$thisfile_ac3_raw_bsi['mixdata']       =        $this->readHeaderBSI(12);
					} elseif ($thisfile_ac3_raw_bsi['mixdef'] == 3) { // mixing option 4
						$mixdefbitsread = 0;
						$thisfile_ac3_raw_bsi['mixdeflen']     =        $this->readHeaderBSI(5); $mixdefbitsread += 5;
						$thisfile_ac3_raw_bsi['flags']['mixdata2'] = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
						if ($thisfile_ac3_raw_bsi['flags']['mixdata2']) {
							$thisfile_ac3_raw_bsi['premixcmpsel']  = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
							$thisfile_ac3_raw_bsi['drcsrc']        = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
							$thisfile_ac3_raw_bsi['premixcmpscl']  =        $this->readHeaderBSI(3); $mixdefbitsread += 3;
							$thisfile_ac3_raw_bsi['flags']['extpgmlscl']   = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
							if ($thisfile_ac3_raw_bsi['flags']['extpgmlscl']) {
								$thisfile_ac3_raw_bsi['extpgmlscl']    =        $this->readHeaderBSI(4); $mixdefbitsread += 4;
							}
							$thisfile_ac3_raw_bsi['flags']['extpgmcscl']   = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
							if ($thisfile_ac3_raw_bsi['flags']['extpgmcscl']) {
								$thisfile_ac3_raw_bsi['extpgmcscl']    =        $this->readHeaderBSI(4); $mixdefbitsread += 4;
							}
							$thisfile_ac3_raw_bsi['flags']['extpgmrscl']   = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
							if ($thisfile_ac3_raw_bsi['flags']['extpgmrscl']) {
								$thisfile_ac3_raw_bsi['extpgmrscl']    =        $this->readHeaderBSI(4);
							}
							$thisfile_ac3_raw_bsi['flags']['extpgmlsscl']  = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
							if ($thisfile_ac3_raw_bsi['flags']['extpgmlsscl']) {
								$thisfile_ac3_raw_bsi['extpgmlsscl']   =        $this->readHeaderBSI(4); $mixdefbitsread += 4;
							}
							$thisfile_ac3_raw_bsi['flags']['extpgmrsscl']  = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
							if ($thisfile_ac3_raw_bsi['flags']['extpgmrsscl']) {
								$thisfile_ac3_raw_bsi['extpgmrsscl']   =        $this->readHeaderBSI(4); $mixdefbitsread += 4;
							}
							$thisfile_ac3_raw_bsi['flags']['extpgmlfescl'] = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
							if ($thisfile_ac3_raw_bsi['flags']['extpgmlfescl']) {
								$thisfile_ac3_raw_bsi['extpgmlfescl']  =        $this->readHeaderBSI(4); $mixdefbitsread += 4;
							}
							$thisfile_ac3_raw_bsi['flags']['dmixscl']      = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
							if ($thisfile_ac3_raw_bsi['flags']['dmixscl']) {
								$thisfile_ac3_raw_bsi['dmixscl']       =        $this->readHeaderBSI(4); $mixdefbitsread += 4;
							}
							$thisfile_ac3_raw_bsi['flags']['addch']        = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
							if ($thisfile_ac3_raw_bsi['flags']['addch']) {
								$thisfile_ac3_raw_bsi['flags']['extpgmaux1scl']   = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
								if ($thisfile_ac3_raw_bsi['flags']['extpgmaux1scl']) {
									$thisfile_ac3_raw_bsi['extpgmaux1scl']    =        $this->readHeaderBSI(4); $mixdefbitsread += 4;
								}
								$thisfile_ac3_raw_bsi['flags']['extpgmaux2scl']   = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
								if ($thisfile_ac3_raw_bsi['flags']['extpgmaux2scl']) {
									$thisfile_ac3_raw_bsi['extpgmaux2scl']    =        $this->readHeaderBSI(4); $mixdefbitsread += 4;
								}
							}
						}
						$thisfile_ac3_raw_bsi['flags']['mixdata3'] = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
						if ($thisfile_ac3_raw_bsi['flags']['mixdata3']) {
							$thisfile_ac3_raw_bsi['spchdat']   =        $this->readHeaderBSI(5); $mixdefbitsread += 5;
							$thisfile_ac3_raw_bsi['flags']['addspchdat'] = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
							if ($thisfile_ac3_raw_bsi['flags']['addspchdat']) {
								$thisfile_ac3_raw_bsi['spchdat1']   =         $this->readHeaderBSI(5); $mixdefbitsread += 5;
								$thisfile_ac3_raw_bsi['spchan1att'] =         $this->readHeaderBSI(2); $mixdefbitsread += 2;
								$thisfile_ac3_raw_bsi['flags']['addspchdat1'] = (bool) $this->readHeaderBSI(1); $mixdefbitsread += 1;
								if ($thisfile_ac3_raw_bsi['flags']['addspchdat1']) {
									$thisfile_ac3_raw_bsi['spchdat2']   =         $this->readHeaderBSI(5); $mixdefbitsread += 5;
									$thisfile_ac3_raw_bsi['spchan2att'] =         $this->readHeaderBSI(3); $mixdefbitsread += 3;
								}
							}
						}
						$mixdata_bits = (8 * ($thisfile_ac3_raw_bsi['mixdeflen'] + 2)) - $mixdefbitsread;
						$mixdata_fill = (($mixdata_bits % 8) ? 8 - ($mixdata_bits % 8) : 0);
						$thisfile_ac3_raw_bsi['mixdata']     =        $this->readHeaderBSI($mixdata_bits);
						$thisfile_ac3_raw_bsi['mixdatafill'] =        $this->readHeaderBSI($mixdata_fill);
						unset($mixdefbitsread, $mixdata_bits, $mixdata_fill);
					}
					if ($thisfile_ac3_raw_bsi['acmod'] < 2) { // if mono or dual mono source
						$thisfile_ac3_raw_bsi['flags']['paninfo'] = (bool) $this->readHeaderBSI(1);
						if ($thisfile_ac3_raw_bsi['flags']['paninfo']) {
							$thisfile_ac3_raw_bsi['panmean']   =        $this->readHeaderBSI(8);
							$thisfile_ac3_raw_bsi['paninfo']   =        $this->readHeaderBSI(6);
						}
						if ($thisfile_ac3_raw_bsi['acmod'] == 0) { // if 1+1 mode (dual mono, so some items need a second value)
							$thisfile_ac3_raw_bsi['flags']['paninfo2'] = (bool) $this->readHeaderBSI(1);
							if ($thisfile_ac3_raw_bsi['flags']['paninfo2']) {
								$thisfile_ac3_raw_bsi['panmean2']   =        $this->readHeaderBSI(8);
								$thisfile_ac3_raw_bsi['paninfo2']   =        $this->readHeaderBSI(6);
							}
						}
					}
					$thisfile_ac3_raw_bsi['flags']['frmmixcfginfo'] = (bool) $this->readHeaderBSI(1);
					if ($thisfile_ac3_raw_bsi['flags']['frmmixcfginfo']) { // mixing configuration information
						if ($thisfile_ac3_raw_bsi['numblkscod'] == 0) {
							$thisfile_ac3_raw_bsi['blkmixcfginfo'][0]  =        $this->readHeaderBSI(5);
						} else {
							for ($blk = 0; $blk < $thisfile_ac3_raw_bsi['numblkscod']; $blk++) {
								$thisfile_ac3_raw_bsi['flags']['blkmixcfginfo'.$blk] = (bool) $this->readHeaderBSI(1);
								if ($thisfile_ac3_raw_bsi['flags']['blkmixcfginfo'.$blk]) { // mixing configuration information
									$thisfile_ac3_raw_bsi['blkmixcfginfo'][$blk]  =        $this->readHeaderBSI(5);
								}
							}
						}
					}
				}
			}
			$thisfile_ac3_raw_bsi['flags']['infomdat']          = (bool) $this->readHeaderBSI(1);
			if ($thisfile_ac3_raw_bsi['flags']['infomdat']) { // Informational metadata
				$thisfile_ac3_raw_bsi['bsmod']                  =        $this->readHeaderBSI(3);
				$thisfile_ac3_raw_bsi['flags']['copyrightb']    = (bool) $this->readHeaderBSI(1);
				$thisfile_ac3_raw_bsi['flags']['origbs']        = (bool) $this->readHeaderBSI(1);
				if ($thisfile_ac3_raw_bsi['acmod'] == 2) { //  if in 2/0 mode
					$thisfile_ac3_raw_bsi['dsurmod']            =        $this->readHeaderBSI(2);
					$thisfile_ac3_raw_bsi['dheadphonmod']       =        $this->readHeaderBSI(2);
				}
				if ($thisfile_ac3_raw_bsi['acmod'] >= 6) { //  if both surround channels exist
					$thisfile_ac3_raw_bsi['dsurexmod']          =        $this->readHeaderBSI(2);
				}
				$thisfile_ac3_raw_bsi['flags']['audprodi']      = (bool) $this->readHeaderBSI(1);
				if ($thisfile_ac3_raw_bsi['flags']['audprodi']) {
					$thisfile_ac3_raw_bsi['mixlevel']           =        $this->readHeaderBSI(5);
					$thisfile_ac3_raw_bsi['roomtyp']            =        $this->readHeaderBSI(2);
					$thisfile_ac3_raw_bsi['flags']['adconvtyp'] = (bool) $this->readHeaderBSI(1);
				}
				if ($thisfile_ac3_raw_bsi['acmod'] == 0) { //  if 1+1 mode (dual mono, so some items need a second value)
					$thisfile_ac3_raw_bsi['flags']['audprodi2']      = (bool) $this->readHeaderBSI(1);
					if ($thisfile_ac3_raw_bsi['flags']['audprodi2']) {
						$thisfile_ac3_raw_bsi['mixlevel2']           =        $this->readHeaderBSI(5);
						$thisfile_ac3_raw_bsi['roomtyp2']            =        $this->readHeaderBSI(2);
						$thisfile_ac3_raw_bsi['flags']['adconvtyp2'] = (bool) $this->readHeaderBSI(1);
					}
				}
				if ($thisfile_ac3_raw_bsi['fscod'] < 3) { // if not half sample rate
					$thisfile_ac3_raw_bsi['flags']['sourcefscod'] = (bool) $this->readHeaderBSI(1);
				}
			}
			if (($thisfile_ac3_raw_bsi['strmtyp'] == 0) && ($thisfile_ac3_raw_bsi['numblkscod'] != 3)) { //  if both surround channels exist
				$thisfile_ac3_raw_bsi['flags']['convsync'] = (bool) $this->readHeaderBSI(1);
			}
			if ($thisfile_ac3_raw_bsi['strmtyp'] == 2) { //  if bit stream converted from AC-3
				if ($thisfile_ac3_raw_bsi['numblkscod'] != 3) { // 6 blocks per syncframe
					$thisfile_ac3_raw_bsi['flags']['blkid']  = 1;
				} else {
					$thisfile_ac3_raw_bsi['flags']['blkid']  = (bool) $this->readHeaderBSI(1);
				}
				if ($thisfile_ac3_raw_bsi['flags']['blkid']) {
					$thisfile_ac3_raw_bsi['frmsizecod']  =        $this->readHeaderBSI(6);
				}
			}
			$thisfile_ac3_raw_bsi['flags']['addbsi']  = (bool) $this->readHeaderBSI(1);
			if ($thisfile_ac3_raw_bsi['flags']['addbsi']) {
				$thisfile_ac3_raw_bsi['addbsil']  =        $this->readHeaderBSI(6);
				$thisfile_ac3_raw_bsi['addbsi']   =        $this->readHeaderBSI(($thisfile_ac3_raw_bsi['addbsil'] + 1) * 8);
			}

		} else {

			$this->error('Bit stream identification is version '.$thisfile_ac3_raw_bsi['bsid'].', but getID3() only understands up to version 16. Please submit a support ticket with a sample file.');
		    unset($info['ac3']);
			return false;

		}

		if (isset($thisfile_ac3_raw_bsi['fscod2'])) {
			$thisfile_ac3['sample_rate'] = self::sampleRateCodeLookup2($thisfile_ac3_raw_bsi['fscod2']);
		} else {
			$thisfile_ac3['sample_rate'] = self::sampleRateCodeLookup($thisfile_ac3_raw_bsi['fscod']);
		}
		if ($thisfile_ac3_raw_bsi['fscod'] <= 3) {
			$info['audio']['sample_rate'] = $thisfile_ac3['sample_rate'];
		} else {
			$this->warning('Unexpected ac3.bsi.fscod value: '.$thisfile_ac3_raw_bsi['fscod']);
		}
		if (isset($thisfile_ac3_raw_bsi['frmsizecod'])) {
			$thisfile_ac3['frame_length'] = self::frameSizeLookup($thisfile_ac3_raw_bsi['frmsizecod'], $thisfile_ac3_raw_bsi['fscod']);
			$thisfile_ac3['bitrate']      = self::bitrateLookup($thisfile_ac3_raw_bsi['frmsizecod']);
		} elseif (!empty($thisfile_ac3_raw_bsi['frmsiz'])) {
// this isn't right, but it's (usually) close, roughly 5% less than it should be.
// but WHERE is the actual bitrate value stored in EAC3?? email info@getid3.org if you know!
			$thisfile_ac3['bitrate']      = ($thisfile_ac3_raw_bsi['frmsiz'] + 1) * 16 * 30; // The frmsiz field shall contain a value one less than the overall size of the coded syncframe in 16-bit words. That is, this field may assume a value ranging from 0 to 2047, and these values correspond to syncframe sizes ranging from 1 to 2048.
// kludge-fix to make it approximately the expected value, still not "right":
$thisfile_ac3['bitrate'] = round(($thisfile_ac3['bitrate'] * 1.05) / 16000) * 16000;
		}
		$info['audio']['bitrate'] = $thisfile_ac3['bitrate'];

		$thisfile_ac3['service_type'] = self::serviceTypeLookup($thisfile_ac3_raw_bsi['bsmod'], $thisfile_ac3_raw_bsi['acmod']);
		$ac3_coding_mode = self::audioCodingModeLookup($thisfile_ac3_raw_bsi['acmod']);
		foreach($ac3_coding_mode as $key => $value) {
			$thisfile_ac3[$key] = $value;
		}
		switch ($thisfile_ac3_raw_bsi['acmod']) {
			case 0:
			case 1:
				$info['audio']['channelmode'] = 'mono';
				break;
			case 3:
			case 4:
				$info['audio']['channelmode'] = 'stereo';
				break;
			default:
				$info['audio']['channelmode'] = 'surround';
				break;
		}
		$info['audio']['channels'] = $thisfile_ac3['num_channels'];

		$thisfile_ac3['lfe_enabled'] = $thisfile_ac3_raw_bsi['flags']['lfeon'];
		if ($thisfile_ac3_raw_bsi['flags']['lfeon']) {
			$info['audio']['channels'] .= '.1';
		}

		$thisfile_ac3['channels_enabled'] = self::channelsEnabledLookup($thisfile_ac3_raw_bsi['acmod'], $thisfile_ac3_raw_bsi['flags']['lfeon']);
		$thisfile_ac3['dialogue_normalization'] = '-'.$thisfile_ac3_raw_bsi['dialnorm'].'dB';

		return true;
	}

	private function readHeaderBSI($length) {
		$data = substr($this->AC3header['bsi'], $this->BSIoffset, $length);
		$this->BSIoffset += $length;

		return bindec($data);
	}

	public static function sampleRateCodeLookup($fscod) {
		static $sampleRateCodeLookup = array(
			0 => 48000,
			1 => 44100,
			2 => 32000,
			3 => 'reserved' // If the reserved code is indicated, the decoder should not attempt to decode audio and should mute.
		);
		return (isset($sampleRateCodeLookup[$fscod]) ? $sampleRateCodeLookup[$fscod] : false);
	}

	public static function sampleRateCodeLookup2($fscod2) {
		static $sampleRateCodeLookup2 = array(
			0 => 24000,
			1 => 22050,
			2 => 16000,
			3 => 'reserved' // If the reserved code is indicated, the decoder should not attempt to decode audio and should mute.
		);
		return (isset($sampleRateCodeLookup2[$fscod2]) ? $sampleRateCodeLookup2[$fscod2] : false);
	}

	public static function serviceTypeLookup($bsmod, $acmod) {
		static $serviceTypeLookup = array();
		if (empty($serviceTypeLookup)) {
			for ($i = 0; $i <= 7; $i++) {
				$serviceTypeLookup[0][$i] = 'main audio service: complete main (CM)';
				$serviceTypeLookup[1][$i] = 'main audio service: music and effects (ME)';
				$serviceTypeLookup[2][$i] = 'associated service: visually impaired (VI)';
				$serviceTypeLookup[3][$i] = 'associated service: hearing impaired (HI)';
				$serviceTypeLookup[4][$i] = 'associated service: dialogue (D)';
				$serviceTypeLookup[5][$i] = 'associated service: commentary (C)';
				$serviceTypeLookup[6][$i] = 'associated service: emergency (E)';
			}

			$serviceTypeLookup[7][1]      = 'associated service: voice over (VO)';
			for ($i = 2; $i <= 7; $i++) {
				$serviceTypeLookup[7][$i] = 'main audio service: karaoke';
			}
		}
		return (isset($serviceTypeLookup[$bsmod][$acmod]) ? $serviceTypeLookup[$bsmod][$acmod] : false);
	}

	public static function audioCodingModeLookup($acmod) {
		// array(channel configuration, # channels (not incl LFE), channel order)
		static $audioCodingModeLookup = array (
			0 => array('channel_config'=>'1+1', 'num_channels'=>2, 'channel_order'=>'Ch1,Ch2'),
			1 => array('channel_config'=>'1/0', 'num_channels'=>1, 'channel_order'=>'C'),
			2 => array('channel_config'=>'2/0', 'num_channels'=>2, 'channel_order'=>'L,R'),
			3 => array('channel_config'=>'3/0', 'num_channels'=>3, 'channel_order'=>'L,C,R'),
			4 => array('channel_config'=>'2/1', 'num_channels'=>3, 'channel_order'=>'L,R,S'),
			5 => array('channel_config'=>'3/1', 'num_channels'=>4, 'channel_order'=>'L,C,R,S'),
			6 => array('channel_config'=>'2/2', 'num_channels'=>4, 'channel_order'=>'L,R,SL,SR'),
			7 => array('channel_config'=>'3/2', 'num_channels'=>5, 'channel_order'=>'L,C,R,SL,SR'),
		);
		return (isset($audioCodingModeLookup[$acmod]) ? $audioCodingModeLookup[$acmod] : false);
	}

	public static function centerMixLevelLookup($cmixlev) {
		static $centerMixLevelLookup;
		if (empty($centerMixLevelLookup)) {
			$centerMixLevelLookup = array(
				0 => pow(2, -3.0 / 6), // 0.707 (-3.0 dB)
				1 => pow(2, -4.5 / 6), // 0.595 (-4.5 dB)
				2 => pow(2, -6.0 / 6), // 0.500 (-6.0 dB)
				3 => 'reserved'
			);
		}
		return (isset($centerMixLevelLookup[$cmixlev]) ? $centerMixLevelLookup[$cmixlev] : false);
	}

	public static function surroundMixLevelLookup($surmixlev) {
		static $surroundMixLevelLookup;
		if (empty($surroundMixLevelLookup)) {
			$surroundMixLevelLookup = array(
				0 => pow(2, -3.0 / 6),
				1 => pow(2, -6.0 / 6),
				2 => 0,
				3 => 'reserved'
			);
		}
		return (isset($surroundMixLevelLookup[$surmixlev]) ? $surroundMixLevelLookup[$surmixlev] : false);
	}

	public static function dolbySurroundModeLookup($dsurmod) {
		static $dolbySurroundModeLookup = array(
			0 => 'not indicated',
			1 => 'Not Dolby Surround encoded',
			2 => 'Dolby Surround encoded',
			3 => 'reserved'
		);
		return (isset($dolbySurroundModeLookup[$dsurmod]) ? $dolbySurroundModeLookup[$dsurmod] : false);
	}

	public static function channelsEnabledLookup($acmod, $lfeon) {
		$lookup = array(
			'ch1'=>(bool) ($acmod == 0),
			'ch2'=>(bool) ($acmod == 0),
			'left'=>(bool) ($acmod > 1),
			'right'=>(bool) ($acmod > 1),
			'center'=>(bool) ($acmod & 0x01),
			'surround_mono'=>false,
			'surround_left'=>false,
			'surround_right'=>false,
			'lfe'=>$lfeon);
		switch ($acmod) {
			case 4:
			case 5:
				$lookup['surround_mono']  = true;
				break;
			case 6:
			case 7:
				$lookup['surround_left']  = true;
				$lookup['surround_right'] = true;
				break;
		}
		return $lookup;
	}

	public static function heavyCompression($compre) {
		// The first four bits indicate gain changes in 6.02dB increments which can be
		// implemented with an arithmetic shift operation. The following four bits
		// indicate linear gain changes, and require a 5-bit multiply.
		// We will represent the two 4-bit fields of compr as follows:
		//   X0 X1 X2 X3 . Y4 Y5 Y6 Y7
		// The meaning of the X values is most simply described by considering X to represent a 4-bit
		// signed integer with values from -8 to +7. The gain indicated by X is then (X + 1) * 6.02 dB. The
		// following table shows this in detail.

		// Meaning of 4 msb of compr
		//  7    +48.16 dB
		//  6    +42.14 dB
		//  5    +36.12 dB
		//  4    +30.10 dB
		//  3    +24.08 dB
		//  2    +18.06 dB
		//  1    +12.04 dB
		//  0     +6.02 dB
		// -1         0 dB
		// -2     -6.02 dB
		// -3    -12.04 dB
		// -4    -18.06 dB
		// -5    -24.08 dB
		// -6    -30.10 dB
		// -7    -36.12 dB
		// -8    -42.14 dB

		$fourbit = str_pad(decbin(($compre & 0xF0) >> 4), 4, '0', STR_PAD_LEFT);
		if ($fourbit{0} == '1') {
			$log_gain = -8 + bindec(substr($fourbit, 1));
		} else {
			$log_gain = bindec(substr($fourbit, 1));
		}
		$log_gain = ($log_gain + 1) * getid3_lib::RGADamplitude2dB(2);

		// The value of Y is a linear representation of a gain change of up to -6 dB. Y is considered to
		// be an unsigned fractional integer, with a leading value of 1, or: 0.1 Y4 Y5 Y6 Y7 (base 2). Y can
		// represent values between 0.111112 (or 31/32) and 0.100002 (or 1/2). Thus, Y can represent gain
		// changes from -0.28 dB to -6.02 dB.

		$lin_gain = (16 + ($compre & 0x0F)) / 32;

		// The combination of X and Y values allows compr to indicate gain changes from
		//  48.16 - 0.28 = +47.89 dB, to
		// -42.14 - 6.02 = -48.16 dB.

		return $log_gain - $lin_gain;
	}

	public static function roomTypeLookup($roomtyp) {
		static $roomTypeLookup = array(
			0 => 'not indicated',
			1 => 'large room, X curve monitor',
			2 => 'small room, flat monitor',
			3 => 'reserved'
		);
		return (isset($roomTypeLookup[$roomtyp]) ? $roomTypeLookup[$roomtyp] : false);
	}

	public static function frameSizeLookup($frmsizecod, $fscod) {
		// LSB is whether padding is used or not
		$padding     = (bool) ($frmsizecod & 0x01);
		$framesizeid =        ($frmsizecod & 0x3E) >> 1;

		static $frameSizeLookup = array();
		if (empty($frameSizeLookup)) {
			$frameSizeLookup = array (
				0  => array( 128,  138,  192),  //  32 kbps
				1  => array( 160,  174,  240),  //  40 kbps
				2  => array( 192,  208,  288),  //  48 kbps
				3  => array( 224,  242,  336),  //  56 kbps
				4  => array( 256,  278,  384),  //  64 kbps
				5  => array( 320,  348,  480),  //  80 kbps
				6  => array( 384,  416,  576),  //  96 kbps
				7  => array( 448,  486,  672),  // 112 kbps
				8  => array( 512,  556,  768),  // 128 kbps
				9  => array( 640,  696,  960),  // 160 kbps
				10 => array( 768,  834, 1152),  // 192 kbps
				11 => array( 896,  974, 1344),  // 224 kbps
				12 => array(1024, 1114, 1536),  // 256 kbps
				13 => array(1280, 1392, 1920),  // 320 kbps
				14 => array(1536, 1670, 2304),  // 384 kbps
				15 => array(1792, 1950, 2688),  // 448 kbps
				16 => array(2048, 2228, 3072),  // 512 kbps
				17 => array(2304, 2506, 3456),  // 576 kbps
				18 => array(2560, 2786, 3840)   // 640 kbps
			);
		}
		if (($fscod == 1) && $padding) {
			// frame lengths are padded by 1 word (16 bits) at 44100
			$frameSizeLookup[$frmsizecod] += 2;
		}
		return (isset($frameSizeLookup[$framesizeid][$fscod]) ? $frameSizeLookup[$framesizeid][$fscod] : false);
	}

	public static function bitrateLookup($frmsizecod) {
		// LSB is whether padding is used or not
		$padding     = (bool) ($frmsizecod & 0x01);
		$framesizeid =        ($frmsizecod & 0x3E) >> 1;

		static $bitrateLookup = array(
			 0 =>  32000,
			 1 =>  40000,
			 2 =>  48000,
			 3 =>  56000,
			 4 =>  64000,
			 5 =>  80000,
			 6 =>  96000,
			 7 => 112000,
			 8 => 128000,
			 9 => 160000,
			10 => 192000,
			11 => 224000,
			12 => 256000,
			13 => 320000,
			14 => 384000,
			15 => 448000,
			16 => 512000,
			17 => 576000,
			18 => 640000,
		);
		return (isset($bitrateLookup[$framesizeid]) ? $bitrateLookup[$framesizeid] : false);
	}

	public static function blocksPerSyncFrame($numblkscod) {
		static $blocksPerSyncFrameLookup = array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 6,
		);
		return (isset($blocksPerSyncFrameLookup[$numblkscod]) ? $blocksPerSyncFrameLookup[$numblkscod] : false);
	}


}
