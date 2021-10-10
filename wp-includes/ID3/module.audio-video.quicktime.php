<?php

/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at https://github.com/JamesHeinrich/getID3       //
//            or https://www.getid3.org                        //
//            or http://getid3.sourceforge.net                 //
//  see readme.txt for more details                            //
/////////////////////////////////////////////////////////////////
//                                                             //
// module.audio-video.quicktime.php                            //
// module for analyzing Quicktime and MP3-in-MP4 files         //
// dependencies: module.audio.mp3.php                          //
// dependencies: module.tag.id3v2.php                          //
//                                                            ///
/////////////////////////////////////////////////////////////////

if (!defined('GETID3_INCLUDEPATH')) { // prevent path-exposing attacks that access modules directly on public webservers
	exit;
}
getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.audio.mp3.php', __FILE__, true);
getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.tag.id3v2.php', __FILE__, true); // needed for ISO 639-2 language code lookup

class getid3_quicktime extends getid3_handler
{

	/** audio-video.quicktime
	 * return all parsed data from all atoms if true, otherwise just returned parsed metadata
	 *
	 * @var bool
	 */
	public $ReturnAtomData        = false;

	/** audio-video.quicktime
	 * return all parsed data from all atoms if true, otherwise just returned parsed metadata
	 *
	 * @var bool
	 */
	public $ParseAllPossibleAtoms = false;

	/**
	 * @return bool
	 */
	public function Analyze() {
		$info = &$this->getid3->info;

		$info['fileformat'] = 'quicktime';
		$info['quicktime']['hinting']    = false;
		$info['quicktime']['controller'] = 'standard'; // may be overridden if 'ctyp' atom is present

		$this->fseek($info['avdataoffset']);

		$offset      = 0;
		$atomcounter = 0;
		$atom_data_read_buffer_size = $info['php_memory_limit'] ? round($info['php_memory_limit'] / 4) : $this->getid3->option_fread_buffer_size * 1024; // set read buffer to 25% of PHP memory limit (if one is specified), otherwise use option_fread_buffer_size [default: 32MB]
		while ($offset < $info['avdataend']) {
			if (!getid3_lib::intValueSupported($offset)) {
				$this->error('Unable to parse atom at offset '.$offset.' because beyond '.round(PHP_INT_MAX / 1073741824).'GB limit of PHP filesystem functions');
				break;
			}
			$this->fseek($offset);
			$AtomHeader = $this->fread(8);

			$atomsize = getid3_lib::BigEndian2Int(substr($AtomHeader, 0, 4));
			$atomname = substr($AtomHeader, 4, 4);

			// 64-bit MOV patch by jlegateØktnc*com
			if ($atomsize == 1) {
				$atomsize = getid3_lib::BigEndian2Int($this->fread(8));
			}

			if (($offset + $atomsize) > $info['avdataend']) {
				$info['quicktime'][$atomname]['name']   = $atomname;
				$info['quicktime'][$atomname]['size']   = $atomsize;
				$info['quicktime'][$atomname]['offset'] = $offset;
				$this->error('Atom at offset '.$offset.' claims to go beyond end-of-file (length: '.$atomsize.' bytes)');
				return false;
			}
			if ($atomsize == 0) {
				// Furthermore, for historical reasons the list of atoms is optionally
				// terminated by a 32-bit integer set to 0. If you are writing a program
				// to read user data atoms, you should allow for the terminating 0.
				$info['quicktime'][$atomname]['name']   = $atomname;
				$info['quicktime'][$atomname]['size']   = $atomsize;
				$info['quicktime'][$atomname]['offset'] = $offset;
				break;
			}

			$atomHierarchy = array();
			$parsedAtomData = $this->QuicktimeParseAtom($atomname, $atomsize, $this->fread(min($atomsize, $atom_data_read_buffer_size)), $offset, $atomHierarchy, $this->ParseAllPossibleAtoms);
			$parsedAtomData['name']   = $atomname;
			$parsedAtomData['size']   = $atomsize;
			$parsedAtomData['offset'] = $offset;
			if (in_array($atomname, array('uuid'))) {
				@$info['quicktime'][$atomname][] = $parsedAtomData;
			} else {
				$info['quicktime'][$atomname] = $parsedAtomData;
			}

			$offset += $atomsize;
			$atomcounter++;
		}

		if (!empty($info['avdataend_tmp'])) {
			// this value is assigned to a temp value and then erased because
			// otherwise any atoms beyond the 'mdat' atom would not get parsed
			$info['avdataend'] = $info['avdataend_tmp'];
			unset($info['avdataend_tmp']);
		}

		if (!empty($info['quicktime']['comments']['chapters']) && is_array($info['quicktime']['comments']['chapters']) && (count($info['quicktime']['comments']['chapters']) > 0)) {
			$durations = $this->quicktime_time_to_sample_table($info);
			for ($i = 0; $i < count($info['quicktime']['comments']['chapters']); $i++) {
				$bookmark = array();
				$bookmark['title'] = $info['quicktime']['comments']['chapters'][$i];
				if (isset($durations[$i])) {
					$bookmark['duration_sample'] = $durations[$i]['sample_duration'];
					if ($i > 0) {
						$bookmark['start_sample'] = $info['quicktime']['bookmarks'][($i - 1)]['start_sample'] + $info['quicktime']['bookmarks'][($i - 1)]['duration_sample'];
					} else {
						$bookmark['start_sample'] = 0;
					}
					if ($time_scale = $this->quicktime_bookmark_time_scale($info)) {
						$bookmark['duration_seconds'] = $bookmark['duration_sample'] / $time_scale;
						$bookmark['start_seconds']    = $bookmark['start_sample']    / $time_scale;
					}
				}
				$info['quicktime']['bookmarks'][] = $bookmark;
			}
		}

		if (isset($info['quicktime']['temp_meta_key_names'])) {
			unset($info['quicktime']['temp_meta_key_names']);
		}

		if (!empty($info['quicktime']['comments']['location.ISO6709'])) {
			// https://en.wikipedia.org/wiki/ISO_6709
			foreach ($info['quicktime']['comments']['location.ISO6709'] as $ISO6709string) {
				$ISO6709parsed = array('latitude'=>false, 'longitude'=>false, 'altitude'=>false);
				if (preg_match('#^([\\+\\-])([0-9]{2}|[0-9]{4}|[0-9]{6})(\\.[0-9]+)?([\\+\\-])([0-9]{3}|[0-9]{5}|[0-9]{7})(\\.[0-9]+)?(([\\+\\-])([0-9]{3}|[0-9]{5}|[0-9]{7})(\\.[0-9]+)?)?/$#', $ISO6709string, $matches)) {
					// phpcs:ignore PHPCompatibility.Lists.AssignmentOrder.Affected
					@list($dummy, $lat_sign, $lat_deg, $lat_deg_dec, $lon_sign, $lon_deg, $lon_deg_dec, $dummy, $alt_sign, $alt_deg, $alt_deg_dec) = $matches;

					if (strlen($lat_deg) == 2) {        // [+-]DD.D
						$ISO6709parsed['latitude'] = (($lat_sign == '-') ? -1 : 1) * floatval(ltrim($lat_deg, '0').$lat_deg_dec);
					} elseif (strlen($lat_deg) == 4) {  // [+-]DDMM.M
						$ISO6709parsed['latitude'] = (($lat_sign == '-') ? -1 : 1) * floatval(ltrim(substr($lat_deg, 0, 2), '0')) + floatval(ltrim(substr($lat_deg, 2, 2), '0').$lat_deg_dec / 60);
					} elseif (strlen($lat_deg) == 6) {  // [+-]DDMMSS.S
						$ISO6709parsed['latitude'] = (($lat_sign == '-') ? -1 : 1) * floatval(ltrim(substr($lat_deg, 0, 2), '0')) + floatval(ltrim(substr($lat_deg, 2, 2), '0') / 60) + floatval(ltrim(substr($lat_deg, 4, 2), '0').$lat_deg_dec / 3600);
					}

					if (strlen($lon_deg) == 3) {        // [+-]DDD.D
						$ISO6709parsed['longitude'] = (($lon_sign == '-') ? -1 : 1) * floatval(ltrim($lon_deg, '0').$lon_deg_dec);
					} elseif (strlen($lon_deg) == 5) {  // [+-]DDDMM.M
						$ISO6709parsed['longitude'] = (($lon_sign == '-') ? -1 : 1) * floatval(ltrim(substr($lon_deg, 0, 2), '0')) + floatval(ltrim(substr($lon_deg, 2, 2), '0').$lon_deg_dec / 60);
					} elseif (strlen($lon_deg) == 7) {  // [+-]DDDMMSS.S
						$ISO6709parsed['longitude'] = (($lon_sign == '-') ? -1 : 1) * floatval(ltrim(substr($lon_deg, 0, 2), '0')) + floatval(ltrim(substr($lon_deg, 2, 2), '0') / 60) + floatval(ltrim(substr($lon_deg, 4, 2), '0').$lon_deg_dec / 3600);
					}

					if (strlen($alt_deg) == 3) {        // [+-]DDD.D
						$ISO6709parsed['altitude'] = (($alt_sign == '-') ? -1 : 1) * floatval(ltrim($alt_deg, '0').$alt_deg_dec);
					} elseif (strlen($alt_deg) == 5) {  // [+-]DDDMM.M
						$ISO6709parsed['altitude'] = (($alt_sign == '-') ? -1 : 1) * floatval(ltrim(substr($alt_deg, 0, 2), '0')) + floatval(ltrim(substr($alt_deg, 2, 2), '0').$alt_deg_dec / 60);
					} elseif (strlen($alt_deg) == 7) {  // [+-]DDDMMSS.S
						$ISO6709parsed['altitude'] = (($alt_sign == '-') ? -1 : 1) * floatval(ltrim(substr($alt_deg, 0, 2), '0')) + floatval(ltrim(substr($alt_deg, 2, 2), '0') / 60) + floatval(ltrim(substr($alt_deg, 4, 2), '0').$alt_deg_dec / 3600);
					}

					foreach (array('latitude', 'longitude', 'altitude') as $key) {
						if ($ISO6709parsed[$key] !== false) {
							$value = (($lat_sign == '-') ? -1 : 1) * floatval($ISO6709parsed[$key]);
							if (!isset($info['quicktime']['comments']['gps_'.$key]) || !in_array($value, $info['quicktime']['comments']['gps_'.$key])) {
								@$info['quicktime']['comments']['gps_'.$key][] = (($lat_sign == '-') ? -1 : 1) * floatval($ISO6709parsed[$key]);
							}
						}
					}
				}
				if ($ISO6709parsed['latitude'] === false) {
					$this->warning('location.ISO6709 string not parsed correctly: "'.$ISO6709string.'", please submit as a bug');
				}
				break;
			}
		}

		if (!isset($info['bitrate']) && !empty($info['playtime_seconds'])) {
			$info['bitrate'] = (($info['avdataend'] - $info['avdataoffset']) * 8) / $info['playtime_seconds'];
		}
		if (isset($info['bitrate']) && !isset($info['audio']['bitrate']) && !isset($info['quicktime']['video'])) {
			$info['audio']['bitrate'] = $info['bitrate'];
		}
		if (!empty($info['bitrate']) && !empty($info['audio']['bitrate']) && empty($info['video']['bitrate']) && !empty($info['video']['frame_rate']) && !empty($info['video']['resolution_x']) && ($info['bitrate'] > $info['audio']['bitrate'])) {
			$info['video']['bitrate'] = $info['bitrate'] - $info['audio']['bitrate'];
		}
		if (!empty($info['playtime_seconds']) && !isset($info['video']['frame_rate']) && !empty($info['quicktime']['stts_framecount'])) {
			foreach ($info['quicktime']['stts_framecount'] as $key => $samples_count) {
				$samples_per_second = $samples_count / $info['playtime_seconds'];
				if ($samples_per_second > 240) {
					// has to be audio samples
				} else {
					$info['video']['frame_rate'] = $samples_per_second;
					break;
				}
			}
		}
		if ($info['audio']['dataformat'] == 'mp4') {
			$info['fileformat'] = 'mp4';
			if (empty($info['video']['resolution_x'])) {
				$info['mime_type']  = 'audio/mp4';
				unset($info['video']['dataformat']);
			} else {
				$info['mime_type']  = 'video/mp4';
			}
		}

		if (!$this->ReturnAtomData) {
			unset($info['quicktime']['moov']);
		}

		if (empty($info['audio']['dataformat']) && !empty($info['quicktime']['audio'])) {
			$info['audio']['dataformat'] = 'quicktime';
		}
		if (empty($info['video']['dataformat']) && !empty($info['quicktime']['video'])) {
			$info['video']['dataformat'] = 'quicktime';
		}
		if (isset($info['video']) && ($info['mime_type'] == 'audio/mp4') && empty($info['video']['resolution_x']) && empty($info['video']['resolution_y']))  {
			unset($info['video']);
		}

		return true;
	}

	/**
	 * @param string $atomname
	 * @param int    $atomsize
	 * @param string $atom_data
	 * @param int    $baseoffset
	 * @param array  $atomHierarchy
	 * @param bool   $ParseAllPossibleAtoms
	 *
	 * @return array|false
	 */
	public function QuicktimeParseAtom($atomname, $atomsize, $atom_data, $baseoffset, &$atomHierarchy, $ParseAllPossibleAtoms) {
		// http://developer.apple.com/techpubs/quicktime/qtdevdocs/APIREF/INDEX/atomalphaindex.htm
		// https://code.google.com/p/mp4v2/wiki/iTunesMetadata

		$info = &$this->getid3->info;

		$atom_parent = end($atomHierarchy); // not array_pop($atomHierarchy); see https://www.getid3.org/phpBB3/viewtopic.php?t=1717
		array_push($atomHierarchy, $atomname);
		$atom_structure              = array();
		$atom_structure['hierarchy'] = implode(' ', $atomHierarchy);
		$atom_structure['name']      = $atomname;
		$atom_structure['size']      = $atomsize;
		$atom_structure['offset']    = $baseoffset;
		if (substr($atomname, 0, 3) == "\x00\x00\x00") {
			// https://github.com/JamesHeinrich/getID3/issues/139
			$atomname = getid3_lib::BigEndian2Int($atomname);
			$atom_structure['name'] = $atomname;
			$atom_structure['subatoms'] = $this->QuicktimeParseContainerAtom($atom_data, $baseoffset + 8, $atomHierarchy, $ParseAllPossibleAtoms);
		} else {
			switch ($atomname) {
				case 'moov': // MOVie container atom
				case 'trak': // TRAcK container atom
				case 'clip': // CLIPping container atom
				case 'matt': // track MATTe container atom
				case 'edts': // EDiTS container atom
				case 'tref': // Track REFerence container atom
				case 'mdia': // MeDIA container atom
				case 'minf': // Media INFormation container atom
				case 'dinf': // Data INFormation container atom
				case 'nmhd': // Null Media HeaDer container atom
				case 'udta': // User DaTA container atom
				case 'cmov': // Compressed MOVie container atom
				case 'rmra': // Reference Movie Record Atom
				case 'rmda': // Reference Movie Descriptor Atom
				case 'gmhd': // Generic Media info HeaDer atom (seen on QTVR)
					$atom_structure['subatoms'] = $this->QuicktimeParseContainerAtom($atom_data, $baseoffset + 8, $atomHierarchy, $ParseAllPossibleAtoms);
					break;

				case 'ilst': // Item LiST container atom
					if ($atom_structure['subatoms'] = $this->QuicktimeParseContainerAtom($atom_data, $baseoffset + 8, $atomHierarchy, $ParseAllPossibleAtoms)) {
						// some "ilst" atoms contain data atoms that have a numeric name, and the data is far more accessible if the returned array is compacted
						$allnumericnames = true;
						foreach ($atom_structure['subatoms'] as $subatomarray) {
							if (!is_integer($subatomarray['name']) || (count($subatomarray['subatoms']) != 1)) {
								$allnumericnames = false;
								break;
							}
						}
						if ($allnumericnames) {
							$newData = array();
							foreach ($atom_structure['subatoms'] as $subatomarray) {
								foreach ($subatomarray['subatoms'] as $newData_subatomarray) {
									unset($newData_subatomarray['hierarchy'], $newData_subatomarray['name']);
									$newData[$subatomarray['name']] = $newData_subatomarray;
									break;
								}
							}
							$atom_structure['data'] = $newData;
							unset($atom_structure['subatoms']);
						}
					}
					break;

				case 'stbl': // Sample TaBLe container atom
					$atom_structure['subatoms'] = $this->QuicktimeParseContainerAtom($atom_data, $baseoffset + 8, $atomHierarchy, $ParseAllPossibleAtoms);
					$isVideo = false;
					$framerate  = 0;
					$framecount = 0;
					foreach ($atom_structure['subatoms'] as $key => $value_array) {
						if (isset($value_array['sample_description_table'])) {
							foreach ($value_array['sample_description_table'] as $key2 => $value_array2) {
								if (isset($value_array2['data_format'])) {
									switch ($value_array2['data_format']) {
										case 'avc1':
										case 'mp4v':
											// video data
											$isVideo = true;
											break;
										case 'mp4a':
											// audio data
											break;
									}
								}
							}
						} elseif (isset($value_array['time_to_sample_table'])) {
							foreach ($value_array['time_to_sample_table'] as $key2 => $value_array2) {
								if (isset($value_array2['sample_count']) && isset($value_array2['sample_duration']) && ($value_array2['sample_duration'] > 0)) {
									$framerate  = round($info['quicktime']['time_scale'] / $value_array2['sample_duration'], 3);
									$framecount = $value_array2['sample_count'];
								}
							}
						}
					}
					if ($isVideo && $framerate) {
						$info['quicktime']['video']['frame_rate'] = $framerate;
						$info['video']['frame_rate'] = $info['quicktime']['video']['frame_rate'];
					}
					if ($isVideo && $framecount) {
						$info['quicktime']['video']['frame_count'] = $framecount;
					}
					break;


				case "\xA9".'alb': // ALBum
				case "\xA9".'ART': //
				case "\xA9".'art': // ARTist
				case "\xA9".'aut': //
				case "\xA9".'cmt': // CoMmenT
				case "\xA9".'com': // COMposer
				case "\xA9".'cpy': //
				case "\xA9".'day': // content created year
				case "\xA9".'dir': //
				case "\xA9".'ed1': //
				case "\xA9".'ed2': //
				case "\xA9".'ed3': //
				case "\xA9".'ed4': //
				case "\xA9".'ed5': //
				case "\xA9".'ed6': //
				case "\xA9".'ed7': //
				case "\xA9".'ed8': //
				case "\xA9".'ed9': //
				case "\xA9".'enc': //
				case "\xA9".'fmt': //
				case "\xA9".'gen': // GENre
				case "\xA9".'grp': // GRouPing
				case "\xA9".'hst': //
				case "\xA9".'inf': //
				case "\xA9".'lyr': // LYRics
				case "\xA9".'mak': //
				case "\xA9".'mod': //
				case "\xA9".'nam': // full NAMe
				case "\xA9".'ope': //
				case "\xA9".'PRD': //
				case "\xA9".'prf': //
				case "\xA9".'req': //
				case "\xA9".'src': //
				case "\xA9".'swr': //
				case "\xA9".'too': // encoder
				case "\xA9".'trk': // TRacK
				case "\xA9".'url': //
				case "\xA9".'wrn': //
				case "\xA9".'wrt': // WRiTer
				case '----': // itunes specific
				case 'aART': // Album ARTist
				case 'akID': // iTunes store account type
				case 'apID': // Purchase Account
				case 'atID': //
				case 'catg': // CaTeGory
				case 'cmID': //
				case 'cnID': //
				case 'covr': // COVeR artwork
				case 'cpil': // ComPILation
				case 'cprt': // CoPyRighT
				case 'desc': // DESCription
				case 'disk': // DISK number
				case 'egid': // Episode Global ID
				case 'geID': //
				case 'gnre': // GeNRE
				case 'hdvd': // HD ViDeo
				case 'keyw': // KEYWord
				case 'ldes': // Long DEScription
				case 'pcst': // PodCaST
				case 'pgap': // GAPless Playback
				case 'plID': //
				case 'purd': // PURchase Date
				case 'purl': // Podcast URL
				case 'rati': //
				case 'rndu': //
				case 'rpdu': //
				case 'rtng': // RaTiNG
				case 'sfID': // iTunes store country
				case 'soaa': // SOrt Album Artist
				case 'soal': // SOrt ALbum
				case 'soar': // SOrt ARtist
				case 'soco': // SOrt COmposer
				case 'sonm': // SOrt NaMe
				case 'sosn': // SOrt Show Name
				case 'stik': //
				case 'tmpo': // TeMPO (BPM)
				case 'trkn': // TRacK Number
				case 'tven': // tvEpisodeID
				case 'tves': // TV EpiSode
				case 'tvnn': // TV Network Name
				case 'tvsh': // TV SHow Name
				case 'tvsn': // TV SeasoN
					if ($atom_parent == 'udta') {
						// User data atom handler
						$atom_structure['data_length'] = getid3_lib::BigEndian2Int(substr($atom_data, 0, 2));
						$atom_structure['language_id'] = getid3_lib::BigEndian2Int(substr($atom_data, 2, 2));
						$atom_structure['data']        =                           substr($atom_data, 4);

						$atom_structure['language']    = $this->QuicktimeLanguageLookup($atom_structure['language_id']);
						if (empty($info['comments']['language']) || (!in_array($atom_structure['language'], $info['comments']['language']))) {
							$info['comments']['language'][] = $atom_structure['language'];
						}
					} else {
						// Apple item list box atom handler
						$atomoffset = 0;
						if (substr($atom_data, 2, 2) == "\x10\xB5") {
							// not sure what it means, but observed on iPhone4 data.
							// Each $atom_data has 2 bytes of datasize, plus 0x10B5, then data
							while ($atomoffset < strlen($atom_data)) {
								$boxsmallsize = getid3_lib::BigEndian2Int(substr($atom_data, $atomoffset,     2));
								$boxsmalltype =                           substr($atom_data, $atomoffset + 2, 2);
								$boxsmalldata =                           substr($atom_data, $atomoffset + 4, $boxsmallsize);
								if ($boxsmallsize <= 1) {
									$this->warning('Invalid QuickTime atom smallbox size "'.$boxsmallsize.'" in atom "'.preg_replace('#[^a-zA-Z0-9 _\\-]#', '?', $atomname).'" at offset: '.($atom_structure['offset'] + $atomoffset));
									$atom_structure['data'] = null;
									$atomoffset = strlen($atom_data);
									break;
								}
								switch ($boxsmalltype) {
									case "\x10\xB5":
										$atom_structure['data'] = $boxsmalldata;
										break;
									default:
										$this->warning('Unknown QuickTime smallbox type: "'.preg_replace('#[^a-zA-Z0-9 _\\-]#', '?', $boxsmalltype).'" ('.trim(getid3_lib::PrintHexBytes($boxsmalltype)).') at offset '.$baseoffset);
										$atom_structure['data'] = $atom_data;
										break;
								}
								$atomoffset += (4 + $boxsmallsize);
							}
						} else {
							while ($atomoffset < strlen($atom_data)) {
								$boxsize = getid3_lib::BigEndian2Int(substr($atom_data, $atomoffset, 4));
								$boxtype =                           substr($atom_data, $atomoffset + 4, 4);
								$boxdata =                           substr($atom_data, $atomoffset + 8, $boxsize - 8);
								if ($boxsize <= 1) {
									$this->warning('Invalid QuickTime atom box size "'.$boxsize.'" in atom "'.preg_replace('#[^a-zA-Z0-9 _\\-]#', '?', $atomname).'" at offset: '.($atom_structure['offset'] + $atomoffset));
									$atom_structure['data'] = null;
									$atomoffset = strlen($atom_data);
									break;
								}
								$atomoffset += $boxsize;

								switch ($boxtype) {
									case 'mean':
									case 'name':
										$atom_structure[$boxtype] = substr($boxdata, 4);
										break;

									case 'data':
										$atom_structure['version']   = getid3_lib::BigEndian2Int(substr($boxdata,  0, 1));
										$atom_structure['flags_raw'] = getid3_lib::BigEndian2Int(substr($boxdata,  1, 3));
										switch ($atom_structure['flags_raw']) {
											case  0: // data flag
											case 21: // tmpo/cpil flag
												switch ($atomname) {
													case 'cpil':
													case 'hdvd':
													case 'pcst':
													case 'pgap':
														// 8-bit integer (boolean)
														$atom_structure['data'] = getid3_lib::BigEndian2Int(substr($boxdata, 8, 1));
														break;

													case 'tmpo':
														// 16-bit integer
														$atom_structure['data'] = getid3_lib::BigEndian2Int(substr($boxdata, 8, 2));
														break;

													case 'disk':
													case 'trkn':
														// binary
														$num       = getid3_lib::BigEndian2Int(substr($boxdata, 10, 2));
														$num_total = getid3_lib::BigEndian2Int(substr($boxdata, 12, 2));
														$atom_structure['data']  = empty($num) ? '' : $num;
														$atom_structure['data'] .= empty($num_total) ? '' : '/'.$num_total;
														break;

													case 'gnre':
														// enum
														$GenreID = getid3_lib::BigEndian2Int(substr($boxdata, 8, 4));
														$atom_structure['data']    = getid3_id3v1::LookupGenreName($GenreID - 1);
														break;

													case 'rtng':
														// 8-bit integer
														$atom_structure[$atomname] = getid3_lib::BigEndian2Int(substr($boxdata, 8, 1));
														$atom_structure['data']    = $this->QuicktimeContentRatingLookup($atom_structure[$atomname]);
														break;

													case 'stik':
														// 8-bit integer (enum)
														$atom_structure[$atomname] = getid3_lib::BigEndian2Int(substr($boxdata, 8, 1));
														$atom_structure['data']    = $this->QuicktimeSTIKLookup($atom_structure[$atomname]);
														break;

													case 'sfID':
														// 32-bit integer
														$atom_structure[$atomname] = getid3_lib::BigEndian2Int(substr($boxdata, 8, 4));
														$atom_structure['data']    = $this->QuicktimeStoreFrontCodeLookup($atom_structure[$atomname]);
														break;

													case 'egid':
													case 'purl':
														$atom_structure['data'] = substr($boxdata, 8);
														break;

													case 'plID':
														// 64-bit integer
														$atom_structure['data'] = getid3_lib::BigEndian2Int(substr($boxdata, 8, 8));
														break;

													case 'covr':
														$atom_structure['data'] = substr($boxdata, 8);
														// not a foolproof check, but better than nothing
														if (preg_match('#^\\xFF\\xD8\\xFF#', $atom_structure['data'])) {
															$atom_structure['image_mime'] = 'image/jpeg';
														} elseif (preg_match('#^\\x89\\x50\\x4E\\x47\\x0D\\x0A\\x1A\\x0A#', $atom_structure['data'])) {
															$atom_structure['image_mime'] = 'image/png';
														} elseif (preg_match('#^GIF#', $atom_structure['data'])) {
															$atom_structure['image_mime'] = 'image/gif';
														}
														$info['quicktime']['comments']['picture'][] = array('image_mime'=>$atom_structure['image_mime'], 'data'=>$atom_structure['data'], 'description'=>'cover');
														break;

													case 'atID':
													case 'cnID':
													case 'geID':
													case 'tves':
													case 'tvsn':
													default:
														// 32-bit integer
														$atom_structure['data'] = getid3_lib::BigEndian2Int(substr($boxdata, 8, 4));
												}
												break;

											case  1: // text flag
											case 13: // image flag
											default:
												$atom_structure['data'] = substr($boxdata, 8);
												if ($atomname == 'covr') {
													if (!empty($atom_structure['data'])) {
														$atom_structure['image_mime'] = 'image/unknown'; // provide default MIME type to ensure array keys exist
														if (function_exists('getimagesizefromstring') && ($getimagesize = getimagesizefromstring($atom_structure['data'])) && !empty($getimagesize['mime'])) {
															$atom_structure['image_mime'] = $getimagesize['mime'];
														} else {
															// if getimagesizefromstring is not available, or fails for some reason, fall back to simple detection of common image formats
															$ImageFormatSignatures = array(
																'image/jpeg' => "\xFF\xD8\xFF",
																'image/png'  => "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A",
																'image/gif'  => 'GIF',
															);
															foreach ($ImageFormatSignatures as $mime => $image_format_signature) {
																if (substr($atom_structure['data'], 0, strlen($image_format_signature)) == $image_format_signature) {
																	$atom_structure['image_mime'] = $mime;
																	break;
																}
															}
														}
														$info['quicktime']['comments']['picture'][] = array('image_mime'=>$atom_structure['image_mime'], 'data'=>$atom_structure['data'], 'description'=>'cover');
													} else {
														$this->warning('Unknown empty "covr" image at offset '.$baseoffset);
													}
												}
												break;

										}
										break;

									default:
										$this->warning('Unknown QuickTime box type: "'.preg_replace('#[^a-zA-Z0-9 _\\-]#', '?', $boxtype).'" ('.trim(getid3_lib::PrintHexBytes($boxtype)).') at offset '.$baseoffset);
										$atom_structure['data'] = $atom_data;

								}
							}
						}
					}
					$this->CopyToAppropriateCommentsSection($atomname, $atom_structure['data'], $atom_structure['name']);
					break;


				case 'play': // auto-PLAY atom
					$atom_structure['autoplay'] = (bool) getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));

					$info['quicktime']['autoplay'] = $atom_structure['autoplay'];
					break;


				case 'WLOC': // Window LOCation atom
					$atom_structure['location_x']  = getid3_lib::BigEndian2Int(substr($atom_data,  0, 2));
					$atom_structure['location_y']  = getid3_lib::BigEndian2Int(substr($atom_data,  2, 2));
					break;


				case 'LOOP': // LOOPing atom
				case 'SelO': // play SELection Only atom
				case 'AllF': // play ALL Frames atom
					$atom_structure['data'] = getid3_lib::BigEndian2Int($atom_data);
					break;


				case 'name': //
				case 'MCPS': // Media Cleaner PRo
				case '@PRM': // adobe PReMiere version
				case '@PRQ': // adobe PRemiere Quicktime version
					$atom_structure['data'] = $atom_data;
					break;


				case 'cmvd': // Compressed MooV Data atom
					// Code by ubergeekØubergeek*tv based on information from
					// http://developer.apple.com/quicktime/icefloe/dispatch012.html
					$atom_structure['unCompressedSize'] = getid3_lib::BigEndian2Int(substr($atom_data, 0, 4));

					$CompressedFileData = substr($atom_data, 4);
					if ($UncompressedHeader = @gzuncompress($CompressedFileData)) {
						$atom_structure['subatoms'] = $this->QuicktimeParseContainerAtom($UncompressedHeader, 0, $atomHierarchy, $ParseAllPossibleAtoms);
					} else {
						$this->warning('Error decompressing compressed MOV atom at offset '.$atom_structure['offset']);
					}
					break;


				case 'dcom': // Data COMpression atom
					$atom_structure['compression_id']   = $atom_data;
					$atom_structure['compression_text'] = $this->QuicktimeDCOMLookup($atom_data);
					break;


				case 'rdrf': // Reference movie Data ReFerence atom
					$atom_structure['version']                = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']              = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3));
					$atom_structure['flags']['internal_data'] = (bool) ($atom_structure['flags_raw'] & 0x000001);

					$atom_structure['reference_type_name']    =                           substr($atom_data,  4, 4);
					$atom_structure['reference_length']       = getid3_lib::BigEndian2Int(substr($atom_data,  8, 4));
					switch ($atom_structure['reference_type_name']) {
						case 'url ':
							$atom_structure['url']            =       $this->NoNullString(substr($atom_data, 12));
							break;

						case 'alis':
							$atom_structure['file_alias']     =                           substr($atom_data, 12);
							break;

						case 'rsrc':
							$atom_structure['resource_alias'] =                           substr($atom_data, 12);
							break;

						default:
							$atom_structure['data']           =                           substr($atom_data, 12);
							break;
					}
					break;


				case 'rmqu': // Reference Movie QUality atom
					$atom_structure['movie_quality'] = getid3_lib::BigEndian2Int($atom_data);
					break;


				case 'rmcs': // Reference Movie Cpu Speed atom
					$atom_structure['version']          = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']        = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['cpu_speed_rating'] = getid3_lib::BigEndian2Int(substr($atom_data,  4, 2));
					break;


				case 'rmvc': // Reference Movie Version Check atom
					$atom_structure['version']            = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']          = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['gestalt_selector']   =                           substr($atom_data,  4, 4);
					$atom_structure['gestalt_value_mask'] = getid3_lib::BigEndian2Int(substr($atom_data,  8, 4));
					$atom_structure['gestalt_value']      = getid3_lib::BigEndian2Int(substr($atom_data, 12, 4));
					$atom_structure['gestalt_check_type'] = getid3_lib::BigEndian2Int(substr($atom_data, 14, 2));
					break;


				case 'rmcd': // Reference Movie Component check atom
					$atom_structure['version']                = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']              = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['component_type']         =                           substr($atom_data,  4, 4);
					$atom_structure['component_subtype']      =                           substr($atom_data,  8, 4);
					$atom_structure['component_manufacturer'] =                           substr($atom_data, 12, 4);
					$atom_structure['component_flags_raw']    = getid3_lib::BigEndian2Int(substr($atom_data, 16, 4));
					$atom_structure['component_flags_mask']   = getid3_lib::BigEndian2Int(substr($atom_data, 20, 4));
					$atom_structure['component_min_version']  = getid3_lib::BigEndian2Int(substr($atom_data, 24, 4));
					break;


				case 'rmdr': // Reference Movie Data Rate atom
					$atom_structure['version']       = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']     = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['data_rate']     = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));

					$atom_structure['data_rate_bps'] = $atom_structure['data_rate'] * 10;
					break;


				case 'rmla': // Reference Movie Language Atom
					$atom_structure['version']     = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']   = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['language_id'] = getid3_lib::BigEndian2Int(substr($atom_data,  4, 2));

					$atom_structure['language']    = $this->QuicktimeLanguageLookup($atom_structure['language_id']);
					if (empty($info['comments']['language']) || (!in_array($atom_structure['language'], $info['comments']['language']))) {
						$info['comments']['language'][] = $atom_structure['language'];
					}
					break;


				case 'ptv ': // Print To Video - defines a movie's full screen mode
					// http://developer.apple.com/documentation/QuickTime/APIREF/SOURCESIV/at_ptv-_pg.htm
					$atom_structure['display_size_raw']  = getid3_lib::BigEndian2Int(substr($atom_data, 0, 2));
					$atom_structure['reserved_1']        = getid3_lib::BigEndian2Int(substr($atom_data, 2, 2)); // hardcoded: 0x0000
					$atom_structure['reserved_2']        = getid3_lib::BigEndian2Int(substr($atom_data, 4, 2)); // hardcoded: 0x0000
					$atom_structure['slide_show_flag']   = getid3_lib::BigEndian2Int(substr($atom_data, 6, 1));
					$atom_structure['play_on_open_flag'] = getid3_lib::BigEndian2Int(substr($atom_data, 7, 1));

					$atom_structure['flags']['play_on_open'] = (bool) $atom_structure['play_on_open_flag'];
					$atom_structure['flags']['slide_show']   = (bool) $atom_structure['slide_show_flag'];

					$ptv_lookup = array(
						0 => 'normal',
						1 => 'double',
						2 => 'half',
						3 => 'full',
						4 => 'current'
					);
					if (isset($ptv_lookup[$atom_structure['display_size_raw']])) {
						$atom_structure['display_size'] = $ptv_lookup[$atom_structure['display_size_raw']];
					} else {
						$this->warning('unknown "ptv " display constant ('.$atom_structure['display_size_raw'].')');
					}
					break;


				case 'stsd': // Sample Table Sample Description atom
					$atom_structure['version']        = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']      = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['number_entries'] = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));

					// see: https://github.com/JamesHeinrich/getID3/issues/111
					// Some corrupt files have been known to have high bits set in the number_entries field
					// This field shouldn't really need to be 32-bits, values stores are likely in the range 1-100000
					// Workaround: mask off the upper byte and throw a warning if it's nonzero
					if ($atom_structure['number_entries'] > 0x000FFFFF) {
						if ($atom_structure['number_entries'] > 0x00FFFFFF) {
							$this->warning('"stsd" atom contains improbably large number_entries (0x'.getid3_lib::PrintHexBytes(substr($atom_data, 4, 4), true, false).' = '.$atom_structure['number_entries'].'), probably in error. Ignoring upper byte and interpreting this as 0x'.getid3_lib::PrintHexBytes(substr($atom_data, 5, 3), true, false).' = '.($atom_structure['number_entries'] & 0x00FFFFFF));
							$atom_structure['number_entries'] = ($atom_structure['number_entries'] & 0x00FFFFFF);
						} else {
							$this->warning('"stsd" atom contains improbably large number_entries (0x'.getid3_lib::PrintHexBytes(substr($atom_data, 4, 4), true, false).' = '.$atom_structure['number_entries'].'), probably in error. Please report this to info@getid3.org referencing bug report #111');
						}
					}

					$stsdEntriesDataOffset = 8;
					for ($i = 0; $i < $atom_structure['number_entries']; $i++) {
						$atom_structure['sample_description_table'][$i]['size']             = getid3_lib::BigEndian2Int(substr($atom_data, $stsdEntriesDataOffset, 4));
						$stsdEntriesDataOffset += 4;
						$atom_structure['sample_description_table'][$i]['data_format']      =                           substr($atom_data, $stsdEntriesDataOffset, 4);
						$stsdEntriesDataOffset += 4;
						$atom_structure['sample_description_table'][$i]['reserved']         = getid3_lib::BigEndian2Int(substr($atom_data, $stsdEntriesDataOffset, 6));
						$stsdEntriesDataOffset += 6;
						$atom_structure['sample_description_table'][$i]['reference_index']  = getid3_lib::BigEndian2Int(substr($atom_data, $stsdEntriesDataOffset, 2));
						$stsdEntriesDataOffset += 2;
						$atom_structure['sample_description_table'][$i]['data']             =                           substr($atom_data, $stsdEntriesDataOffset, ($atom_structure['sample_description_table'][$i]['size'] - 4 - 4 - 6 - 2));
						$stsdEntriesDataOffset += ($atom_structure['sample_description_table'][$i]['size'] - 4 - 4 - 6 - 2);

						if (substr($atom_structure['sample_description_table'][$i]['data'],  1, 54) == 'application/octet-stream;type=com.parrot.videometadata') {
							// special handling for apparently-malformed (TextMetaDataSampleEntry?) data for some version of Parrot drones
							$atom_structure['sample_description_table'][$i]['parrot_frame_metadata']['mime_type']        =       substr($atom_structure['sample_description_table'][$i]['data'],  1, 55);
							$atom_structure['sample_description_table'][$i]['parrot_frame_metadata']['metadata_version'] = (int) substr($atom_structure['sample_description_table'][$i]['data'], 55,  1);
							unset($atom_structure['sample_description_table'][$i]['data']);
$this->warning('incomplete/incorrect handling of "stsd" with Parrot metadata in this version of getID3() ['.$this->getid3->version().']');
							continue;
						}

						$atom_structure['sample_description_table'][$i]['encoder_version']  = getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'],  0, 2));
						$atom_structure['sample_description_table'][$i]['encoder_revision'] = getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'],  2, 2));
						$atom_structure['sample_description_table'][$i]['encoder_vendor']   =                           substr($atom_structure['sample_description_table'][$i]['data'],  4, 4);

						switch ($atom_structure['sample_description_table'][$i]['encoder_vendor']) {

							case "\x00\x00\x00\x00":
								// audio tracks
								$atom_structure['sample_description_table'][$i]['audio_channels']       =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'],  8,  2));
								$atom_structure['sample_description_table'][$i]['audio_bit_depth']      =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 10,  2));
								$atom_structure['sample_description_table'][$i]['audio_compression_id'] =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 12,  2));
								$atom_structure['sample_description_table'][$i]['audio_packet_size']    =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 14,  2));
								$atom_structure['sample_description_table'][$i]['audio_sample_rate']    = getid3_lib::FixedPoint16_16(substr($atom_structure['sample_description_table'][$i]['data'], 16,  4));

								// video tracks
								// http://developer.apple.com/library/mac/#documentation/QuickTime/QTFF/QTFFChap3/qtff3.html
								$atom_structure['sample_description_table'][$i]['temporal_quality'] =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'],  8,  4));
								$atom_structure['sample_description_table'][$i]['spatial_quality']  =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 12,  4));
								$atom_structure['sample_description_table'][$i]['width']            =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 16,  2));
								$atom_structure['sample_description_table'][$i]['height']           =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 18,  2));
								$atom_structure['sample_description_table'][$i]['resolution_x']     = getid3_lib::FixedPoint16_16(substr($atom_structure['sample_description_table'][$i]['data'], 24,  4));
								$atom_structure['sample_description_table'][$i]['resolution_y']     = getid3_lib::FixedPoint16_16(substr($atom_structure['sample_description_table'][$i]['data'], 28,  4));
								$atom_structure['sample_description_table'][$i]['data_size']        =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 32,  4));
								$atom_structure['sample_description_table'][$i]['frame_count']      =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 36,  2));
								$atom_structure['sample_description_table'][$i]['compressor_name']  =                             substr($atom_structure['sample_description_table'][$i]['data'], 38,  4);
								$atom_structure['sample_description_table'][$i]['pixel_depth']      =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 42,  2));
								$atom_structure['sample_description_table'][$i]['color_table_id']   =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 44,  2));

								switch ($atom_structure['sample_description_table'][$i]['data_format']) {
									case '2vuY':
									case 'avc1':
									case 'cvid':
									case 'dvc ':
									case 'dvcp':
									case 'gif ':
									case 'h263':
									case 'jpeg':
									case 'kpcd':
									case 'mjpa':
									case 'mjpb':
									case 'mp4v':
									case 'png ':
									case 'raw ':
									case 'rle ':
									case 'rpza':
									case 'smc ':
									case 'SVQ1':
									case 'SVQ3':
									case 'tiff':
									case 'v210':
									case 'v216':
									case 'v308':
									case 'v408':
									case 'v410':
									case 'yuv2':
										$info['fileformat'] = 'mp4';
										$info['video']['fourcc'] = $atom_structure['sample_description_table'][$i]['data_format'];
										if ($this->QuicktimeVideoCodecLookup($info['video']['fourcc'])) {
											$info['video']['fourcc_lookup'] = $this->QuicktimeVideoCodecLookup($info['video']['fourcc']);
										}

										// https://www.getid3.org/phpBB3/viewtopic.php?t=1550
										//if ((!empty($atom_structure['sample_description_table'][$i]['width']) && !empty($atom_structure['sample_description_table'][$i]['width'])) && (empty($info['video']['resolution_x']) || empty($info['video']['resolution_y']) || (number_format($info['video']['resolution_x'], 6) != number_format(round($info['video']['resolution_x']), 6)) || (number_format($info['video']['resolution_y'], 6) != number_format(round($info['video']['resolution_y']), 6)))) { // ugly check for floating point numbers
										if (!empty($atom_structure['sample_description_table'][$i]['width']) && !empty($atom_structure['sample_description_table'][$i]['height'])) {
											// assume that values stored here are more important than values stored in [tkhd] atom
											$info['video']['resolution_x'] = $atom_structure['sample_description_table'][$i]['width'];
											$info['video']['resolution_y'] = $atom_structure['sample_description_table'][$i]['height'];
											$info['quicktime']['video']['resolution_x'] = $info['video']['resolution_x'];
											$info['quicktime']['video']['resolution_y'] = $info['video']['resolution_y'];
										}
										break;

									case 'qtvr':
										$info['video']['dataformat'] = 'quicktimevr';
										break;

									case 'mp4a':
									default:
										$info['quicktime']['audio']['codec']       = $this->QuicktimeAudioCodecLookup($atom_structure['sample_description_table'][$i]['data_format']);
										$info['quicktime']['audio']['sample_rate'] = $atom_structure['sample_description_table'][$i]['audio_sample_rate'];
										$info['quicktime']['audio']['channels']    = $atom_structure['sample_description_table'][$i]['audio_channels'];
										$info['quicktime']['audio']['bit_depth']   = $atom_structure['sample_description_table'][$i]['audio_bit_depth'];
										$info['audio']['codec']                    = $info['quicktime']['audio']['codec'];
										$info['audio']['sample_rate']              = $info['quicktime']['audio']['sample_rate'];
										$info['audio']['channels']                 = $info['quicktime']['audio']['channels'];
										$info['audio']['bits_per_sample']          = $info['quicktime']['audio']['bit_depth'];
										switch ($atom_structure['sample_description_table'][$i]['data_format']) {
											case 'raw ': // PCM
											case 'alac': // Apple Lossless Audio Codec
											case 'sowt': // signed/two's complement (Little Endian)
											case 'twos': // signed/two's complement (Big Endian)
											case 'in24': // 24-bit Integer
											case 'in32': // 32-bit Integer
											case 'fl32': // 32-bit Floating Point
											case 'fl64': // 64-bit Floating Point
												$info['audio']['lossless'] = $info['quicktime']['audio']['lossless'] = true;
												$info['audio']['bitrate']  = $info['quicktime']['audio']['bitrate']  = $info['audio']['channels'] * $info['audio']['bits_per_sample'] * $info['audio']['sample_rate'];
												break;
											default:
												$info['audio']['lossless'] = false;
												break;
										}
										break;
								}
								break;

							default:
								switch ($atom_structure['sample_description_table'][$i]['data_format']) {
									case 'mp4s':
										$info['fileformat'] = 'mp4';
										break;

									default:
										// video atom
										$atom_structure['sample_description_table'][$i]['video_temporal_quality']  =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'],  8,  4));
										$atom_structure['sample_description_table'][$i]['video_spatial_quality']   =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 12,  4));
										$atom_structure['sample_description_table'][$i]['video_frame_width']       =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 16,  2));
										$atom_structure['sample_description_table'][$i]['video_frame_height']      =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 18,  2));
										$atom_structure['sample_description_table'][$i]['video_resolution_x']      = getid3_lib::FixedPoint16_16(substr($atom_structure['sample_description_table'][$i]['data'], 20,  4));
										$atom_structure['sample_description_table'][$i]['video_resolution_y']      = getid3_lib::FixedPoint16_16(substr($atom_structure['sample_description_table'][$i]['data'], 24,  4));
										$atom_structure['sample_description_table'][$i]['video_data_size']         =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 28,  4));
										$atom_structure['sample_description_table'][$i]['video_frame_count']       =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 32,  2));
										$atom_structure['sample_description_table'][$i]['video_encoder_name_len']  =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 34,  1));
										$atom_structure['sample_description_table'][$i]['video_encoder_name']      =                             substr($atom_structure['sample_description_table'][$i]['data'], 35, $atom_structure['sample_description_table'][$i]['video_encoder_name_len']);
										$atom_structure['sample_description_table'][$i]['video_pixel_color_depth'] =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 66,  2));
										$atom_structure['sample_description_table'][$i]['video_color_table_id']    =   getid3_lib::BigEndian2Int(substr($atom_structure['sample_description_table'][$i]['data'], 68,  2));

										$atom_structure['sample_description_table'][$i]['video_pixel_color_type']  = (((int) $atom_structure['sample_description_table'][$i]['video_pixel_color_depth'] > 32) ? 'grayscale' : 'color');
										$atom_structure['sample_description_table'][$i]['video_pixel_color_name']  = $this->QuicktimeColorNameLookup($atom_structure['sample_description_table'][$i]['video_pixel_color_depth']);

										if ($atom_structure['sample_description_table'][$i]['video_pixel_color_name'] != 'invalid') {
											$info['quicktime']['video']['codec_fourcc']        = $atom_structure['sample_description_table'][$i]['data_format'];
											$info['quicktime']['video']['codec_fourcc_lookup'] = $this->QuicktimeVideoCodecLookup($atom_structure['sample_description_table'][$i]['data_format']);
											$info['quicktime']['video']['codec']               = (((int) $atom_structure['sample_description_table'][$i]['video_encoder_name_len'] > 0) ? $atom_structure['sample_description_table'][$i]['video_encoder_name'] : $atom_structure['sample_description_table'][$i]['data_format']);
											$info['quicktime']['video']['color_depth']         = $atom_structure['sample_description_table'][$i]['video_pixel_color_depth'];
											$info['quicktime']['video']['color_depth_name']    = $atom_structure['sample_description_table'][$i]['video_pixel_color_name'];

											$info['video']['codec']           = $info['quicktime']['video']['codec'];
											$info['video']['bits_per_sample'] = $info['quicktime']['video']['color_depth'];
										}
										$info['video']['lossless']           = false;
										$info['video']['pixel_aspect_ratio'] = (float) 1;
										break;
								}
								break;
						}
						switch (strtolower($atom_structure['sample_description_table'][$i]['data_format'])) {
							case 'mp4a':
								$info['audio']['dataformat']         = 'mp4';
								$info['quicktime']['audio']['codec'] = 'mp4';
								break;

							case '3ivx':
							case '3iv1':
							case '3iv2':
								$info['video']['dataformat'] = '3ivx';
								break;

							case 'xvid':
								$info['video']['dataformat'] = 'xvid';
								break;

							case 'mp4v':
								$info['video']['dataformat'] = 'mpeg4';
								break;

							case 'divx':
							case 'div1':
							case 'div2':
							case 'div3':
							case 'div4':
							case 'div5':
							case 'div6':
								$info['video']['dataformat'] = 'divx';
								break;

							default:
								// do nothing
								break;
						}
						unset($atom_structure['sample_description_table'][$i]['data']);
					}
					break;


				case 'stts': // Sample Table Time-to-Sample atom
					$atom_structure['version']        = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']      = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['number_entries'] = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
					$sttsEntriesDataOffset = 8;
					//$FrameRateCalculatorArray = array();
					$frames_count = 0;

					$max_stts_entries_to_scan = ($info['php_memory_limit'] ? min(floor($this->getid3->memory_limit / 10000), $atom_structure['number_entries']) : $atom_structure['number_entries']);
					if ($max_stts_entries_to_scan < $atom_structure['number_entries']) {
						$this->warning('QuickTime atom "stts" has '.$atom_structure['number_entries'].' but only scanning the first '.$max_stts_entries_to_scan.' entries due to limited PHP memory available ('.floor($this->getid3->memory_limit / 1048576).'MB).');
					}
					for ($i = 0; $i < $max_stts_entries_to_scan; $i++) {
						$atom_structure['time_to_sample_table'][$i]['sample_count']    = getid3_lib::BigEndian2Int(substr($atom_data, $sttsEntriesDataOffset, 4));
						$sttsEntriesDataOffset += 4;
						$atom_structure['time_to_sample_table'][$i]['sample_duration'] = getid3_lib::BigEndian2Int(substr($atom_data, $sttsEntriesDataOffset, 4));
						$sttsEntriesDataOffset += 4;

						$frames_count += $atom_structure['time_to_sample_table'][$i]['sample_count'];

						// THIS SECTION REPLACED WITH CODE IN "stbl" ATOM
						//if (!empty($info['quicktime']['time_scale']) && ($atom_structure['time_to_sample_table'][$i]['sample_duration'] > 0)) {
						//	$stts_new_framerate = $info['quicktime']['time_scale'] / $atom_structure['time_to_sample_table'][$i]['sample_duration'];
						//	if ($stts_new_framerate <= 60) {
						//		// some atoms have durations of "1" giving a very large framerate, which probably is not right
						//		$info['video']['frame_rate'] = max($info['video']['frame_rate'], $stts_new_framerate);
						//	}
						//}
						//
						//$FrameRateCalculatorArray[($info['quicktime']['time_scale'] / $atom_structure['time_to_sample_table'][$i]['sample_duration'])] += $atom_structure['time_to_sample_table'][$i]['sample_count'];
					}
					$info['quicktime']['stts_framecount'][] = $frames_count;
					//$sttsFramesTotal  = 0;
					//$sttsSecondsTotal = 0;
					//foreach ($FrameRateCalculatorArray as $frames_per_second => $frame_count) {
					//	if (($frames_per_second > 60) || ($frames_per_second < 1)) {
					//		// not video FPS information, probably audio information
					//		$sttsFramesTotal  = 0;
					//		$sttsSecondsTotal = 0;
					//		break;
					//	}
					//	$sttsFramesTotal  += $frame_count;
					//	$sttsSecondsTotal += $frame_count / $frames_per_second;
					//}
					//if (($sttsFramesTotal > 0) && ($sttsSecondsTotal > 0)) {
					//	if (($sttsFramesTotal / $sttsSecondsTotal) > $info['video']['frame_rate']) {
					//		$info['video']['frame_rate'] = $sttsFramesTotal / $sttsSecondsTotal;
					//	}
					//}
					break;


				case 'stss': // Sample Table Sync Sample (key frames) atom
					if ($ParseAllPossibleAtoms) {
						$atom_structure['version']        = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
						$atom_structure['flags_raw']      = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
						$atom_structure['number_entries'] = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
						$stssEntriesDataOffset = 8;
						for ($i = 0; $i < $atom_structure['number_entries']; $i++) {
							$atom_structure['time_to_sample_table'][$i] = getid3_lib::BigEndian2Int(substr($atom_data, $stssEntriesDataOffset, 4));
							$stssEntriesDataOffset += 4;
						}
					}
					break;


				case 'stsc': // Sample Table Sample-to-Chunk atom
					if ($ParseAllPossibleAtoms) {
						$atom_structure['version']        = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
						$atom_structure['flags_raw']      = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
						$atom_structure['number_entries'] = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
						$stscEntriesDataOffset = 8;
						for ($i = 0; $i < $atom_structure['number_entries']; $i++) {
							$atom_structure['sample_to_chunk_table'][$i]['first_chunk']        = getid3_lib::BigEndian2Int(substr($atom_data, $stscEntriesDataOffset, 4));
							$stscEntriesDataOffset += 4;
							$atom_structure['sample_to_chunk_table'][$i]['samples_per_chunk']  = getid3_lib::BigEndian2Int(substr($atom_data, $stscEntriesDataOffset, 4));
							$stscEntriesDataOffset += 4;
							$atom_structure['sample_to_chunk_table'][$i]['sample_description'] = getid3_lib::BigEndian2Int(substr($atom_data, $stscEntriesDataOffset, 4));
							$stscEntriesDataOffset += 4;
						}
					}
					break;


				case 'stsz': // Sample Table SiZe atom
					if ($ParseAllPossibleAtoms) {
						$atom_structure['version']        = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
						$atom_structure['flags_raw']      = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
						$atom_structure['sample_size']    = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
						$atom_structure['number_entries'] = getid3_lib::BigEndian2Int(substr($atom_data,  8, 4));
						$stszEntriesDataOffset = 12;
						if ($atom_structure['sample_size'] == 0) {
							for ($i = 0; $i < $atom_structure['number_entries']; $i++) {
								$atom_structure['sample_size_table'][$i] = getid3_lib::BigEndian2Int(substr($atom_data, $stszEntriesDataOffset, 4));
								$stszEntriesDataOffset += 4;
							}
						}
					}
					break;


				case 'stco': // Sample Table Chunk Offset atom
//					if (true) {
					if ($ParseAllPossibleAtoms) {
						$atom_structure['version']        = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
						$atom_structure['flags_raw']      = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
						$atom_structure['number_entries'] = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
						$stcoEntriesDataOffset = 8;
						for ($i = 0; $i < $atom_structure['number_entries']; $i++) {
							$atom_structure['chunk_offset_table'][$i] = getid3_lib::BigEndian2Int(substr($atom_data, $stcoEntriesDataOffset, 4));
							$stcoEntriesDataOffset += 4;
						}
					}
					break;


				case 'co64': // Chunk Offset 64-bit (version of "stco" that supports > 2GB files)
					if ($ParseAllPossibleAtoms) {
						$atom_structure['version']        = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
						$atom_structure['flags_raw']      = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
						$atom_structure['number_entries'] = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
						$stcoEntriesDataOffset = 8;
						for ($i = 0; $i < $atom_structure['number_entries']; $i++) {
							$atom_structure['chunk_offset_table'][$i] = getid3_lib::BigEndian2Int(substr($atom_data, $stcoEntriesDataOffset, 8));
							$stcoEntriesDataOffset += 8;
						}
					}
					break;


				case 'dref': // Data REFerence atom
					$atom_structure['version']        = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']      = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['number_entries'] = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
					$drefDataOffset = 8;
					for ($i = 0; $i < $atom_structure['number_entries']; $i++) {
						$atom_structure['data_references'][$i]['size']                    = getid3_lib::BigEndian2Int(substr($atom_data, $drefDataOffset, 4));
						$drefDataOffset += 4;
						$atom_structure['data_references'][$i]['type']                    =                           substr($atom_data, $drefDataOffset, 4);
						$drefDataOffset += 4;
						$atom_structure['data_references'][$i]['version']                 = getid3_lib::BigEndian2Int(substr($atom_data,  $drefDataOffset, 1));
						$drefDataOffset += 1;
						$atom_structure['data_references'][$i]['flags_raw']               = getid3_lib::BigEndian2Int(substr($atom_data,  $drefDataOffset, 3)); // hardcoded: 0x0000
						$drefDataOffset += 3;
						$atom_structure['data_references'][$i]['data']                    =                           substr($atom_data, $drefDataOffset, ($atom_structure['data_references'][$i]['size'] - 4 - 4 - 1 - 3));
						$drefDataOffset += ($atom_structure['data_references'][$i]['size'] - 4 - 4 - 1 - 3);

						$atom_structure['data_references'][$i]['flags']['self_reference'] = (bool) ($atom_structure['data_references'][$i]['flags_raw'] & 0x001);
					}
					break;


				case 'gmin': // base Media INformation atom
					$atom_structure['version']                = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']              = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['graphics_mode']          = getid3_lib::BigEndian2Int(substr($atom_data,  4, 2));
					$atom_structure['opcolor_red']            = getid3_lib::BigEndian2Int(substr($atom_data,  6, 2));
					$atom_structure['opcolor_green']          = getid3_lib::BigEndian2Int(substr($atom_data,  8, 2));
					$atom_structure['opcolor_blue']           = getid3_lib::BigEndian2Int(substr($atom_data, 10, 2));
					$atom_structure['balance']                = getid3_lib::BigEndian2Int(substr($atom_data, 12, 2));
					$atom_structure['reserved']               = getid3_lib::BigEndian2Int(substr($atom_data, 14, 2));
					break;


				case 'smhd': // Sound Media information HeaDer atom
					$atom_structure['version']                = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']              = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['balance']                = getid3_lib::BigEndian2Int(substr($atom_data,  4, 2));
					$atom_structure['reserved']               = getid3_lib::BigEndian2Int(substr($atom_data,  6, 2));
					break;


				case 'vmhd': // Video Media information HeaDer atom
					$atom_structure['version']                = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']              = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3));
					$atom_structure['graphics_mode']          = getid3_lib::BigEndian2Int(substr($atom_data,  4, 2));
					$atom_structure['opcolor_red']            = getid3_lib::BigEndian2Int(substr($atom_data,  6, 2));
					$atom_structure['opcolor_green']          = getid3_lib::BigEndian2Int(substr($atom_data,  8, 2));
					$atom_structure['opcolor_blue']           = getid3_lib::BigEndian2Int(substr($atom_data, 10, 2));

					$atom_structure['flags']['no_lean_ahead'] = (bool) ($atom_structure['flags_raw'] & 0x001);
					break;


				case 'hdlr': // HanDLeR reference atom
					$atom_structure['version']                = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']              = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['component_type']         =                           substr($atom_data,  4, 4);
					$atom_structure['component_subtype']      =                           substr($atom_data,  8, 4);
					$atom_structure['component_manufacturer'] =                           substr($atom_data, 12, 4);
					$atom_structure['component_flags_raw']    = getid3_lib::BigEndian2Int(substr($atom_data, 16, 4));
					$atom_structure['component_flags_mask']   = getid3_lib::BigEndian2Int(substr($atom_data, 20, 4));
					$atom_structure['component_name']         = $this->MaybePascal2String(substr($atom_data, 24));

					if (($atom_structure['component_subtype'] == 'STpn') && ($atom_structure['component_manufacturer'] == 'zzzz')) {
						$info['video']['dataformat'] = 'quicktimevr';
					}
					break;


				case 'mdhd': // MeDia HeaDer atom
					$atom_structure['version']               = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']             = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['creation_time']         = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
					$atom_structure['modify_time']           = getid3_lib::BigEndian2Int(substr($atom_data,  8, 4));
					$atom_structure['time_scale']            = getid3_lib::BigEndian2Int(substr($atom_data, 12, 4));
					$atom_structure['duration']              = getid3_lib::BigEndian2Int(substr($atom_data, 16, 4));
					$atom_structure['language_id']           = getid3_lib::BigEndian2Int(substr($atom_data, 20, 2));
					$atom_structure['quality']               = getid3_lib::BigEndian2Int(substr($atom_data, 22, 2));

					if ($atom_structure['time_scale'] == 0) {
						$this->error('Corrupt Quicktime file: mdhd.time_scale == zero');
						return false;
					}
					$info['quicktime']['time_scale'] = ((isset($info['quicktime']['time_scale']) && ($info['quicktime']['time_scale'] < 1000)) ? max($info['quicktime']['time_scale'], $atom_structure['time_scale']) : $atom_structure['time_scale']);

					$atom_structure['creation_time_unix']    = getid3_lib::DateMac2Unix($atom_structure['creation_time']);
					$atom_structure['modify_time_unix']      = getid3_lib::DateMac2Unix($atom_structure['modify_time']);
					$atom_structure['playtime_seconds']      = $atom_structure['duration'] / $atom_structure['time_scale'];
					$atom_structure['language']              = $this->QuicktimeLanguageLookup($atom_structure['language_id']);
					if (empty($info['comments']['language']) || (!in_array($atom_structure['language'], $info['comments']['language']))) {
						$info['comments']['language'][] = $atom_structure['language'];
					}
					$info['quicktime']['timestamps_unix']['create'][$atom_structure['hierarchy']] = $atom_structure['creation_time_unix'];
					$info['quicktime']['timestamps_unix']['modify'][$atom_structure['hierarchy']] = $atom_structure['modify_time_unix'];
					break;


				case 'pnot': // Preview atom
					$atom_structure['modification_date']      = getid3_lib::BigEndian2Int(substr($atom_data,  0, 4)); // "standard Macintosh format"
					$atom_structure['version_number']         = getid3_lib::BigEndian2Int(substr($atom_data,  4, 2)); // hardcoded: 0x00
					$atom_structure['atom_type']              =                           substr($atom_data,  6, 4);        // usually: 'PICT'
					$atom_structure['atom_index']             = getid3_lib::BigEndian2Int(substr($atom_data, 10, 2)); // usually: 0x01

					$atom_structure['modification_date_unix'] = getid3_lib::DateMac2Unix($atom_structure['modification_date']);
					$info['quicktime']['timestamps_unix']['modify'][$atom_structure['hierarchy']] = $atom_structure['modification_date_unix'];
					break;


				case 'crgn': // Clipping ReGioN atom
					$atom_structure['region_size']   = getid3_lib::BigEndian2Int(substr($atom_data,  0, 2)); // The Region size, Region boundary box,
					$atom_structure['boundary_box']  = getid3_lib::BigEndian2Int(substr($atom_data,  2, 8)); // and Clipping region data fields
					$atom_structure['clipping_data'] =                           substr($atom_data, 10);           // constitute a QuickDraw region.
					break;


				case 'load': // track LOAD settings atom
					$atom_structure['preload_start_time'] = getid3_lib::BigEndian2Int(substr($atom_data,  0, 4));
					$atom_structure['preload_duration']   = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
					$atom_structure['preload_flags_raw']  = getid3_lib::BigEndian2Int(substr($atom_data,  8, 4));
					$atom_structure['default_hints_raw']  = getid3_lib::BigEndian2Int(substr($atom_data, 12, 4));

					$atom_structure['default_hints']['double_buffer'] = (bool) ($atom_structure['default_hints_raw'] & 0x0020);
					$atom_structure['default_hints']['high_quality']  = (bool) ($atom_structure['default_hints_raw'] & 0x0100);
					break;


				case 'tmcd': // TiMe CoDe atom
				case 'chap': // CHAPter list atom
				case 'sync': // SYNChronization atom
				case 'scpt': // tranSCriPT atom
				case 'ssrc': // non-primary SouRCe atom
					for ($i = 0; $i < strlen($atom_data); $i += 4) {
						@$atom_structure['track_id'][] = getid3_lib::BigEndian2Int(substr($atom_data, $i, 4));
					}
					break;


				case 'elst': // Edit LiST atom
					$atom_structure['version']        = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']      = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['number_entries'] = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
					for ($i = 0; $i < $atom_structure['number_entries']; $i++ ) {
						$atom_structure['edit_list'][$i]['track_duration'] =   getid3_lib::BigEndian2Int(substr($atom_data, 8 + ($i * 12) + 0, 4));
						$atom_structure['edit_list'][$i]['media_time']     =   getid3_lib::BigEndian2Int(substr($atom_data, 8 + ($i * 12) + 4, 4));
						$atom_structure['edit_list'][$i]['media_rate']     = getid3_lib::FixedPoint16_16(substr($atom_data, 8 + ($i * 12) + 8, 4));
					}
					break;


				case 'kmat': // compressed MATte atom
					$atom_structure['version']        = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']      = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3)); // hardcoded: 0x0000
					$atom_structure['matte_data_raw'] =               substr($atom_data,  4);
					break;


				case 'ctab': // Color TABle atom
					$atom_structure['color_table_seed']   = getid3_lib::BigEndian2Int(substr($atom_data,  0, 4)); // hardcoded: 0x00000000
					$atom_structure['color_table_flags']  = getid3_lib::BigEndian2Int(substr($atom_data,  4, 2)); // hardcoded: 0x8000
					$atom_structure['color_table_size']   = getid3_lib::BigEndian2Int(substr($atom_data,  6, 2)) + 1;
					for ($colortableentry = 0; $colortableentry < $atom_structure['color_table_size']; $colortableentry++) {
						$atom_structure['color_table'][$colortableentry]['alpha'] = getid3_lib::BigEndian2Int(substr($atom_data, 8 + ($colortableentry * 8) + 0, 2));
						$atom_structure['color_table'][$colortableentry]['red']   = getid3_lib::BigEndian2Int(substr($atom_data, 8 + ($colortableentry * 8) + 2, 2));
						$atom_structure['color_table'][$colortableentry]['green'] = getid3_lib::BigEndian2Int(substr($atom_data, 8 + ($colortableentry * 8) + 4, 2));
						$atom_structure['color_table'][$colortableentry]['blue']  = getid3_lib::BigEndian2Int(substr($atom_data, 8 + ($colortableentry * 8) + 6, 2));
					}
					break;


				case 'mvhd': // MoVie HeaDer atom
					$atom_structure['version']            =   getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']          =   getid3_lib::BigEndian2Int(substr($atom_data,  1, 3));
					$atom_structure['creation_time']      =   getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
					$atom_structure['modify_time']        =   getid3_lib::BigEndian2Int(substr($atom_data,  8, 4));
					$atom_structure['time_scale']         =   getid3_lib::BigEndian2Int(substr($atom_data, 12, 4));
					$atom_structure['duration']           =   getid3_lib::BigEndian2Int(substr($atom_data, 16, 4));
					$atom_structure['preferred_rate']     = getid3_lib::FixedPoint16_16(substr($atom_data, 20, 4));
					$atom_structure['preferred_volume']   =   getid3_lib::FixedPoint8_8(substr($atom_data, 24, 2));
					$atom_structure['reserved']           =                             substr($atom_data, 26, 10);
					$atom_structure['matrix_a']           = getid3_lib::FixedPoint16_16(substr($atom_data, 36, 4));
					$atom_structure['matrix_b']           = getid3_lib::FixedPoint16_16(substr($atom_data, 40, 4));
					$atom_structure['matrix_u']           =  getid3_lib::FixedPoint2_30(substr($atom_data, 44, 4));
					$atom_structure['matrix_c']           = getid3_lib::FixedPoint16_16(substr($atom_data, 48, 4));
					$atom_structure['matrix_d']           = getid3_lib::FixedPoint16_16(substr($atom_data, 52, 4));
					$atom_structure['matrix_v']           =  getid3_lib::FixedPoint2_30(substr($atom_data, 56, 4));
					$atom_structure['matrix_x']           = getid3_lib::FixedPoint16_16(substr($atom_data, 60, 4));
					$atom_structure['matrix_y']           = getid3_lib::FixedPoint16_16(substr($atom_data, 64, 4));
					$atom_structure['matrix_w']           =  getid3_lib::FixedPoint2_30(substr($atom_data, 68, 4));
					$atom_structure['preview_time']       =   getid3_lib::BigEndian2Int(substr($atom_data, 72, 4));
					$atom_structure['preview_duration']   =   getid3_lib::BigEndian2Int(substr($atom_data, 76, 4));
					$atom_structure['poster_time']        =   getid3_lib::BigEndian2Int(substr($atom_data, 80, 4));
					$atom_structure['selection_time']     =   getid3_lib::BigEndian2Int(substr($atom_data, 84, 4));
					$atom_structure['selection_duration'] =   getid3_lib::BigEndian2Int(substr($atom_data, 88, 4));
					$atom_structure['current_time']       =   getid3_lib::BigEndian2Int(substr($atom_data, 92, 4));
					$atom_structure['next_track_id']      =   getid3_lib::BigEndian2Int(substr($atom_data, 96, 4));

					if ($atom_structure['time_scale'] == 0) {
						$this->error('Corrupt Quicktime file: mvhd.time_scale == zero');
						return false;
					}
					$atom_structure['creation_time_unix']        = getid3_lib::DateMac2Unix($atom_structure['creation_time']);
					$atom_structure['modify_time_unix']          = getid3_lib::DateMac2Unix($atom_structure['modify_time']);
					$info['quicktime']['timestamps_unix']['create'][$atom_structure['hierarchy']] = $atom_structure['creation_time_unix'];
					$info['quicktime']['timestamps_unix']['modify'][$atom_structure['hierarchy']] = $atom_structure['modify_time_unix'];
					$info['quicktime']['time_scale']    = ((isset($info['quicktime']['time_scale']) && ($info['quicktime']['time_scale'] < 1000)) ? max($info['quicktime']['time_scale'], $atom_structure['time_scale']) : $atom_structure['time_scale']);
					$info['quicktime']['display_scale'] = $atom_structure['matrix_a'];
					$info['playtime_seconds']           = $atom_structure['duration'] / $atom_structure['time_scale'];
					break;


				case 'tkhd': // TracK HeaDer atom
					$atom_structure['version']             =   getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']           =   getid3_lib::BigEndian2Int(substr($atom_data,  1, 3));
					$atom_structure['creation_time']       =   getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
					$atom_structure['modify_time']         =   getid3_lib::BigEndian2Int(substr($atom_data,  8, 4));
					$atom_structure['trackid']             =   getid3_lib::BigEndian2Int(substr($atom_data, 12, 4));
					$atom_structure['reserved1']           =   getid3_lib::BigEndian2Int(substr($atom_data, 16, 4));
					$atom_structure['duration']            =   getid3_lib::BigEndian2Int(substr($atom_data, 20, 4));
					$atom_structure['reserved2']           =   getid3_lib::BigEndian2Int(substr($atom_data, 24, 8));
					$atom_structure['layer']               =   getid3_lib::BigEndian2Int(substr($atom_data, 32, 2));
					$atom_structure['alternate_group']     =   getid3_lib::BigEndian2Int(substr($atom_data, 34, 2));
					$atom_structure['volume']              =   getid3_lib::FixedPoint8_8(substr($atom_data, 36, 2));
					$atom_structure['reserved3']           =   getid3_lib::BigEndian2Int(substr($atom_data, 38, 2));
					// http://developer.apple.com/library/mac/#documentation/QuickTime/RM/MovieBasics/MTEditing/K-Chapter/11MatrixFunctions.html
					// http://developer.apple.com/library/mac/#documentation/QuickTime/qtff/QTFFChap4/qtff4.html#//apple_ref/doc/uid/TP40000939-CH206-18737
					$atom_structure['matrix_a']            = getid3_lib::FixedPoint16_16(substr($atom_data, 40, 4));
					$atom_structure['matrix_b']            = getid3_lib::FixedPoint16_16(substr($atom_data, 44, 4));
					$atom_structure['matrix_u']            =  getid3_lib::FixedPoint2_30(substr($atom_data, 48, 4));
					$atom_structure['matrix_c']            = getid3_lib::FixedPoint16_16(substr($atom_data, 52, 4));
					$atom_structure['matrix_d']            = getid3_lib::FixedPoint16_16(substr($atom_data, 56, 4));
					$atom_structure['matrix_v']            =  getid3_lib::FixedPoint2_30(substr($atom_data, 60, 4));
					$atom_structure['matrix_x']            = getid3_lib::FixedPoint16_16(substr($atom_data, 64, 4));
					$atom_structure['matrix_y']            = getid3_lib::FixedPoint16_16(substr($atom_data, 68, 4));
					$atom_structure['matrix_w']            =  getid3_lib::FixedPoint2_30(substr($atom_data, 72, 4));
					$atom_structure['width']               = getid3_lib::FixedPoint16_16(substr($atom_data, 76, 4));
					$atom_structure['height']              = getid3_lib::FixedPoint16_16(substr($atom_data, 80, 4));
					$atom_structure['flags']['enabled']    = (bool) ($atom_structure['flags_raw'] & 0x0001);
					$atom_structure['flags']['in_movie']   = (bool) ($atom_structure['flags_raw'] & 0x0002);
					$atom_structure['flags']['in_preview'] = (bool) ($atom_structure['flags_raw'] & 0x0004);
					$atom_structure['flags']['in_poster']  = (bool) ($atom_structure['flags_raw'] & 0x0008);
					$atom_structure['creation_time_unix']  = getid3_lib::DateMac2Unix($atom_structure['creation_time']);
					$atom_structure['modify_time_unix']    = getid3_lib::DateMac2Unix($atom_structure['modify_time']);
					$info['quicktime']['timestamps_unix']['create'][$atom_structure['hierarchy']] = $atom_structure['creation_time_unix'];
					$info['quicktime']['timestamps_unix']['modify'][$atom_structure['hierarchy']] = $atom_structure['modify_time_unix'];

					// https://www.getid3.org/phpBB3/viewtopic.php?t=1908
					// attempt to compute rotation from matrix values
					// 2017-Dec-28: uncertain if 90/270 are correctly oriented; values returned by FixedPoint16_16 should perhaps be -1 instead of 65535(?)
					$matrixRotation = 0;
					switch ($atom_structure['matrix_a'].':'.$atom_structure['matrix_b'].':'.$atom_structure['matrix_c'].':'.$atom_structure['matrix_d']) {
						case '1:0:0:1':         $matrixRotation =   0; break;
						case '0:1:65535:0':     $matrixRotation =  90; break;
						case '65535:0:0:65535': $matrixRotation = 180; break;
						case '0:65535:1:0':     $matrixRotation = 270; break;
						default: break;
					}

					// https://www.getid3.org/phpBB3/viewtopic.php?t=2468
					// The rotation matrix can appear in the Quicktime file multiple times, at least once for each track,
					// and it's possible that only the video track (or, in theory, one of the video tracks) is flagged as
					// rotated while the other tracks (e.g. audio) is tagged as rotation=0 (behavior noted on iPhone 8 Plus)
					// The correct solution would be to check if the TrackID associated with the rotation matrix is indeed
					// a video track (or the main video track) and only set the rotation then, but since information about
					// what track is what is not trivially there to be examined, the lazy solution is to set the rotation
					// if it is found to be nonzero, on the assumption that tracks that don't need it will have rotation set
					// to zero (and be effectively ignored) and the video track will have rotation set correctly, which will
					// either be zero and automatically correct, or nonzero and be set correctly.
					if (!isset($info['video']['rotate']) || (($info['video']['rotate'] == 0) && ($matrixRotation > 0))) {
						$info['quicktime']['video']['rotate'] = $info['video']['rotate'] = $matrixRotation;
					}

					if ($atom_structure['flags']['enabled'] == 1) {
						if (!isset($info['video']['resolution_x']) || !isset($info['video']['resolution_y'])) {
							$info['video']['resolution_x'] = $atom_structure['width'];
							$info['video']['resolution_y'] = $atom_structure['height'];
						}
						$info['video']['resolution_x'] = max($info['video']['resolution_x'], $atom_structure['width']);
						$info['video']['resolution_y'] = max($info['video']['resolution_y'], $atom_structure['height']);
						$info['quicktime']['video']['resolution_x'] = $info['video']['resolution_x'];
						$info['quicktime']['video']['resolution_y'] = $info['video']['resolution_y'];
					} else {
						// see: https://www.getid3.org/phpBB3/viewtopic.php?t=1295
						//if (isset($info['video']['resolution_x'])) { unset($info['video']['resolution_x']); }
						//if (isset($info['video']['resolution_y'])) { unset($info['video']['resolution_y']); }
						//if (isset($info['quicktime']['video']))    { unset($info['quicktime']['video']);    }
					}
					break;


				case 'iods': // Initial Object DeScriptor atom
					// http://www.koders.com/c/fid1FAB3E762903DC482D8A246D4A4BF9F28E049594.aspx?s=windows.h
					// http://libquicktime.sourcearchive.com/documentation/1.0.2plus-pdebian/iods_8c-source.html
					$offset = 0;
					$atom_structure['version']                =       getid3_lib::BigEndian2Int(substr($atom_data, $offset, 1));
					$offset += 1;
					$atom_structure['flags_raw']              =       getid3_lib::BigEndian2Int(substr($atom_data, $offset, 3));
					$offset += 3;
					$atom_structure['mp4_iod_tag']            =       getid3_lib::BigEndian2Int(substr($atom_data, $offset, 1));
					$offset += 1;
					$atom_structure['length']                 = $this->quicktime_read_mp4_descr_length($atom_data, $offset);
					//$offset already adjusted by quicktime_read_mp4_descr_length()
					$atom_structure['object_descriptor_id']   =       getid3_lib::BigEndian2Int(substr($atom_data, $offset, 2));
					$offset += 2;
					$atom_structure['od_profile_level']       =       getid3_lib::BigEndian2Int(substr($atom_data, $offset, 1));
					$offset += 1;
					$atom_structure['scene_profile_level']    =       getid3_lib::BigEndian2Int(substr($atom_data, $offset, 1));
					$offset += 1;
					$atom_structure['audio_profile_id']       =       getid3_lib::BigEndian2Int(substr($atom_data, $offset, 1));
					$offset += 1;
					$atom_structure['video_profile_id']       =       getid3_lib::BigEndian2Int(substr($atom_data, $offset, 1));
					$offset += 1;
					$atom_structure['graphics_profile_level'] =       getid3_lib::BigEndian2Int(substr($atom_data, $offset, 1));
					$offset += 1;

					$atom_structure['num_iods_tracks'] = ($atom_structure['length'] - 7) / 6; // 6 bytes would only be right if all tracks use 1-byte length fields
					for ($i = 0; $i < $atom_structure['num_iods_tracks']; $i++) {
						$atom_structure['track'][$i]['ES_ID_IncTag'] =       getid3_lib::BigEndian2Int(substr($atom_data, $offset, 1));
						$offset += 1;
						$atom_structure['track'][$i]['length']       = $this->quicktime_read_mp4_descr_length($atom_data, $offset);
						//$offset already adjusted by quicktime_read_mp4_descr_length()
						$atom_structure['track'][$i]['track_id']     =       getid3_lib::BigEndian2Int(substr($atom_data, $offset, 4));
						$offset += 4;
					}

					$atom_structure['audio_profile_name'] = $this->QuicktimeIODSaudioProfileName($atom_structure['audio_profile_id']);
					$atom_structure['video_profile_name'] = $this->QuicktimeIODSvideoProfileName($atom_structure['video_profile_id']);
					break;

				case 'ftyp': // FileTYPe (?) atom (for MP4 it seems)
					$atom_structure['signature'] =                           substr($atom_data,  0, 4);
					$atom_structure['unknown_1'] = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
					$atom_structure['fourcc']    =                           substr($atom_data,  8, 4);
					break;

				case 'mdat': // Media DATa atom
					// 'mdat' contains the actual data for the audio/video, possibly also subtitles

	/* due to lack of known documentation, this is a kludge implementation. If you know of documentation on how mdat is properly structed, please send it to info@getid3.org */

					// first, skip any 'wide' padding, and second 'mdat' header (with specified size of zero?)
					$mdat_offset = 0;
					while (true) {
						if (substr($atom_data, $mdat_offset, 8) == "\x00\x00\x00\x08".'wide') {
							$mdat_offset += 8;
						} elseif (substr($atom_data, $mdat_offset, 8) == "\x00\x00\x00\x00".'mdat') {
							$mdat_offset += 8;
						} else {
							break;
						}
					}
					if (substr($atom_data, $mdat_offset, 4) == 'GPRO') {
						$GOPRO_chunk_length = getid3_lib::LittleEndian2Int(substr($atom_data, $mdat_offset + 4, 4));
						$GOPRO_offset = 8;
						$atom_structure['GPRO']['raw'] = substr($atom_data, $mdat_offset + 8, $GOPRO_chunk_length - 8);
						$atom_structure['GPRO']['firmware'] = substr($atom_structure['GPRO']['raw'],  0, 15);
						$atom_structure['GPRO']['unknown1'] = substr($atom_structure['GPRO']['raw'], 15, 16);
						$atom_structure['GPRO']['unknown2'] = substr($atom_structure['GPRO']['raw'], 31, 32);
						$atom_structure['GPRO']['unknown3'] = substr($atom_structure['GPRO']['raw'], 63, 16);
						$atom_structure['GPRO']['camera']   = substr($atom_structure['GPRO']['raw'], 79, 32);
						$info['quicktime']['camera']['model'] = rtrim($atom_structure['GPRO']['camera'], "\x00");
					}

					// check to see if it looks like chapter titles, in the form of unterminated strings with a leading 16-bit size field
					while (($mdat_offset < (strlen($atom_data) - 8))
						&& ($chapter_string_length = getid3_lib::BigEndian2Int(substr($atom_data, $mdat_offset, 2)))
						&& ($chapter_string_length < 1000)
						&& ($chapter_string_length <= (strlen($atom_data) - $mdat_offset - 2))
						&& preg_match('#^([\x00-\xFF]{2})([\x20-\xFF]+)$#', substr($atom_data, $mdat_offset, $chapter_string_length + 2), $chapter_matches)) {
							list($dummy, $chapter_string_length_hex, $chapter_string) = $chapter_matches;
							$mdat_offset += (2 + $chapter_string_length);
							@$info['quicktime']['comments']['chapters'][] = $chapter_string;

							// "encd" atom specifies encoding. In theory could be anything, almost always UTF-8, but may be UTF-16 with BOM (not currently handled)
							if (substr($atom_data, $mdat_offset, 12) == "\x00\x00\x00\x0C\x65\x6E\x63\x64\x00\x00\x01\x00") { // UTF-8
								$mdat_offset += 12;
							}
					}

					if (($atomsize > 8) && (!isset($info['avdataend_tmp']) || ($info['quicktime'][$atomname]['size'] > ($info['avdataend_tmp'] - $info['avdataoffset'])))) {

						$info['avdataoffset'] = $atom_structure['offset'] + 8;                       // $info['quicktime'][$atomname]['offset'] + 8;
						$OldAVDataEnd         = $info['avdataend'];
						$info['avdataend']    = $atom_structure['offset'] + $atom_structure['size']; // $info['quicktime'][$atomname]['offset'] + $info['quicktime'][$atomname]['size'];

						$getid3_temp = new getID3();
						$getid3_temp->openfile($this->getid3->filename, $this->getid3->info['filesize'], $this->getid3->fp);
						$getid3_temp->info['avdataoffset'] = $info['avdataoffset'];
						$getid3_temp->info['avdataend']    = $info['avdataend'];
						$getid3_mp3 = new getid3_mp3($getid3_temp);
						if ($getid3_mp3->MPEGaudioHeaderValid($getid3_mp3->MPEGaudioHeaderDecode($this->fread(4)))) {
							$getid3_mp3->getOnlyMPEGaudioInfo($getid3_temp->info['avdataoffset'], false);
							if (!empty($getid3_temp->info['warning'])) {
								foreach ($getid3_temp->info['warning'] as $value) {
									$this->warning($value);
								}
							}
							if (!empty($getid3_temp->info['mpeg'])) {
								$info['mpeg'] = $getid3_temp->info['mpeg'];
								if (isset($info['mpeg']['audio'])) {
									$info['audio']['dataformat']   = 'mp3';
									$info['audio']['codec']        = (!empty($info['mpeg']['audio']['encoder']) ? $info['mpeg']['audio']['encoder'] : (!empty($info['mpeg']['audio']['codec']) ? $info['mpeg']['audio']['codec'] : (!empty($info['mpeg']['audio']['LAME']) ? 'LAME' :'mp3')));
									$info['audio']['sample_rate']  = $info['mpeg']['audio']['sample_rate'];
									$info['audio']['channels']     = $info['mpeg']['audio']['channels'];
									$info['audio']['bitrate']      = $info['mpeg']['audio']['bitrate'];
									$info['audio']['bitrate_mode'] = strtolower($info['mpeg']['audio']['bitrate_mode']);
									$info['bitrate']               = $info['audio']['bitrate'];
								}
							}
						}
						unset($getid3_mp3, $getid3_temp);
						$info['avdataend'] = $OldAVDataEnd;
						unset($OldAVDataEnd);

					}

					unset($mdat_offset, $chapter_string_length, $chapter_matches);
					break;

				case 'free': // FREE space atom
				case 'skip': // SKIP atom
				case 'wide': // 64-bit expansion placeholder atom
					// 'free', 'skip' and 'wide' are just padding, contains no useful data at all

					// When writing QuickTime files, it is sometimes necessary to update an atom's size.
					// It is impossible to update a 32-bit atom to a 64-bit atom since the 32-bit atom
					// is only 8 bytes in size, and the 64-bit atom requires 16 bytes. Therefore, QuickTime
					// puts an 8-byte placeholder atom before any atoms it may have to update the size of.
					// In this way, if the atom needs to be converted from a 32-bit to a 64-bit atom, the
					// placeholder atom can be overwritten to obtain the necessary 8 extra bytes.
					// The placeholder atom has a type of kWideAtomPlaceholderType ( 'wide' ).
					break;


				case 'nsav': // NoSAVe atom
					// http://developer.apple.com/technotes/tn/tn2038.html
					$atom_structure['data'] = getid3_lib::BigEndian2Int(substr($atom_data,  0, 4));
					break;

				case 'ctyp': // Controller TYPe atom (seen on QTVR)
					// http://homepages.slingshot.co.nz/~helmboy/quicktime/formats/qtm-layout.txt
					// some controller names are:
					//   0x00 + 'std' for linear movie
					//   'none' for no controls
					$atom_structure['ctyp'] = substr($atom_data, 0, 4);
					$info['quicktime']['controller'] = $atom_structure['ctyp'];
					switch ($atom_structure['ctyp']) {
						case 'qtvr':
							$info['video']['dataformat'] = 'quicktimevr';
							break;
					}
					break;

				case 'pano': // PANOrama track (seen on QTVR)
					$atom_structure['pano'] = getid3_lib::BigEndian2Int(substr($atom_data,  0, 4));
					break;

				case 'hint': // HINT track
				case 'hinf': //
				case 'hinv': //
				case 'hnti': //
					$info['quicktime']['hinting'] = true;
					break;

				case 'imgt': // IMaGe Track reference (kQTVRImageTrackRefType) (seen on QTVR)
					for ($i = 0; $i < ($atom_structure['size'] - 8); $i += 4) {
						$atom_structure['imgt'][] = getid3_lib::BigEndian2Int(substr($atom_data, $i, 4));
					}
					break;


				// Observed-but-not-handled atom types are just listed here to prevent warnings being generated
				case 'FXTC': // Something to do with Adobe After Effects (?)
				case 'PrmA':
				case 'code':
				case 'FIEL': // this is NOT "fiel" (Field Ordering) as describe here: http://developer.apple.com/documentation/QuickTime/QTFF/QTFFChap3/chapter_4_section_2.html
				case 'tapt': // TrackApertureModeDimensionsAID - http://developer.apple.com/documentation/QuickTime/Reference/QT7-1_Update_Reference/Constants/Constants.html
							// tapt seems to be used to compute the video size [https://www.getid3.org/phpBB3/viewtopic.php?t=838]
							// * http://lists.apple.com/archives/quicktime-api/2006/Aug/msg00014.html
							// * http://handbrake.fr/irclogs/handbrake-dev/handbrake-dev20080128_pg2.html
				case 'ctts'://  STCompositionOffsetAID             - http://developer.apple.com/documentation/QuickTime/Reference/QTRef_Constants/Reference/reference.html
				case 'cslg'://  STCompositionShiftLeastGreatestAID - http://developer.apple.com/documentation/QuickTime/Reference/QTRef_Constants/Reference/reference.html
				case 'sdtp'://  STSampleDependencyAID              - http://developer.apple.com/documentation/QuickTime/Reference/QTRef_Constants/Reference/reference.html
				case 'stps'://  STPartialSyncSampleAID             - http://developer.apple.com/documentation/QuickTime/Reference/QTRef_Constants/Reference/reference.html
					//$atom_structure['data'] = $atom_data;
					break;

				case "\xA9".'xyz':  // GPS latitude+longitude+altitude
					$atom_structure['data'] = $atom_data;
					if (preg_match('#([\\+\\-][0-9\\.]+)([\\+\\-][0-9\\.]+)([\\+\\-][0-9\\.]+)?/$#i', $atom_data, $matches)) {
						@list($all, $latitude, $longitude, $altitude) = $matches;
						$info['quicktime']['comments']['gps_latitude'][]  = floatval($latitude);
						$info['quicktime']['comments']['gps_longitude'][] = floatval($longitude);
						if (!empty($altitude)) {
							$info['quicktime']['comments']['gps_altitude'][] = floatval($altitude);
						}
					} else {
						$this->warning('QuickTime atom "©xyz" data does not match expected data pattern at offset '.$baseoffset.'. Please report as getID3() bug.');
					}
					break;

				case 'NCDT':
					// https://exiftool.org/TagNames/Nikon.html
					// Nikon-specific QuickTime tags found in the NCDT atom of MOV videos from some Nikon cameras such as the Coolpix S8000 and D5100
					$atom_structure['subatoms'] = $this->QuicktimeParseContainerAtom($atom_data, $baseoffset + 4, $atomHierarchy, $ParseAllPossibleAtoms);
					break;
				case 'NCTH': // Nikon Camera THumbnail image
				case 'NCVW': // Nikon Camera preVieW image
				case 'NCM1': // Nikon Camera preview iMage 1
				case 'NCM2': // Nikon Camera preview iMage 2
					// https://exiftool.org/TagNames/Nikon.html
					if (preg_match('/^\xFF\xD8\xFF/', $atom_data)) {
						$descriptions = array(
							'NCTH' => 'Nikon Camera Thumbnail Image',
							'NCVW' => 'Nikon Camera Preview Image',
							'NCM1' => 'Nikon Camera Preview Image 1',
							'NCM2' => 'Nikon Camera Preview Image 2',
						);
						$atom_structure['data'] = $atom_data;
						$atom_structure['image_mime'] = 'image/jpeg';
						$atom_structure['description'] = isset($descriptions[$atomname]) ? $descriptions[$atomname] : 'Nikon preview image';
						$info['quicktime']['comments']['picture'][] = array(
							'image_mime' => $atom_structure['image_mime'],
							'data' => $atom_data,
							'description' => $atom_structure['description']
						);
					}
					break;
				case 'NCTG': // Nikon - https://exiftool.org/TagNames/Nikon.html#NCTG
					getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.tag.nikon-nctg.php', __FILE__, true);
					$nikonNCTG = new getid3_tag_nikon_nctg($this->getid3);

					$atom_structure['data'] = $nikonNCTG->parse($atom_data);
					break;
				case 'NCHD': // Nikon:MakerNoteVersion  - https://exiftool.org/TagNames/Nikon.html
					$makerNoteVersion = '';
					for ($i = 0, $iMax = strlen($atom_data); $i < $iMax; ++$i) {
						if (ord($atom_data[$i]) >= 0x00 && ord($atom_data[$i]) <= 0x1F) {
							$makerNoteVersion .= ' '.ord($atom_data[$i]);
						} else {
							$makerNoteVersion .= $atom_data[$i];
						}
					}
					$makerNoteVersion = rtrim($makerNoteVersion, "\x00");
					$atom_structure['data'] = array(
						'MakerNoteVersion' => $makerNoteVersion
					);
					break;
				case 'NCDB': // Nikon                   - https://exiftool.org/TagNames/Nikon.html
				case 'CNCV': // Canon:CompressorVersion - https://exiftool.org/TagNames/Canon.html
					$atom_structure['data'] = $atom_data;
					break;

				case "\x00\x00\x00\x00":
					// some kind of metacontainer, may contain a big data dump such as:
					// mdta keys \005 mdtacom.apple.quicktime.make (mdtacom.apple.quicktime.creationdate ,mdtacom.apple.quicktime.location.ISO6709 $mdtacom.apple.quicktime.software !mdtacom.apple.quicktime.model ilst \01D \001 \015data \001DE\010Apple 0 \002 (data \001DE\0102011-05-11T17:54:04+0200 2 \003 *data \001DE\010+52.4936+013.3897+040.247/ \01D \004 \015data \001DE\0104.3.1 \005 \018data \001DE\010iPhone 4
					// https://xhelmboyx.tripod.com/formats/qti-layout.txt

					$atom_structure['version']   =          getid3_lib::BigEndian2Int(substr($atom_data, 0, 1));
					$atom_structure['flags_raw'] =          getid3_lib::BigEndian2Int(substr($atom_data, 1, 3));
					$atom_structure['subatoms']  = $this->QuicktimeParseContainerAtom(substr($atom_data, 4), $baseoffset + 8, $atomHierarchy, $ParseAllPossibleAtoms);
					//$atom_structure['subatoms']  = $this->QuicktimeParseContainerAtom($atom_data, $baseoffset + 8, $atomHierarchy, $ParseAllPossibleAtoms);
					break;

				case 'meta': // METAdata atom
					// https://developer.apple.com/library/mac/documentation/QuickTime/QTFF/Metadata/Metadata.html

					$atom_structure['version']   =          getid3_lib::BigEndian2Int(substr($atom_data, 0, 1));
					$atom_structure['flags_raw'] =          getid3_lib::BigEndian2Int(substr($atom_data, 1, 3));
					$atom_structure['subatoms']  = $this->QuicktimeParseContainerAtom($atom_data, $baseoffset + 8, $atomHierarchy, $ParseAllPossibleAtoms);
					break;

				case 'data': // metaDATA atom
					static $metaDATAkey = 1; // real ugly, but so is the QuickTime structure that stores keys and values in different multinested locations that are hard to relate to each other
					// seems to be 2 bytes language code (ASCII), 2 bytes unknown (set to 0x10B5 in sample I have), remainder is useful data
					$atom_structure['language'] =                           substr($atom_data, 4 + 0, 2);
					$atom_structure['unknown']  = getid3_lib::BigEndian2Int(substr($atom_data, 4 + 2, 2));
					$atom_structure['data']     =                           substr($atom_data, 4 + 4);
					$atom_structure['key_name'] = @$info['quicktime']['temp_meta_key_names'][$metaDATAkey++];

					if ($atom_structure['key_name'] && $atom_structure['data']) {
						@$info['quicktime']['comments'][str_replace('com.apple.quicktime.', '', $atom_structure['key_name'])][] = $atom_structure['data'];
					}
					break;

				case 'keys': // KEYS that may be present in the metadata atom.
					// https://developer.apple.com/library/mac/documentation/QuickTime/QTFF/Metadata/Metadata.html#//apple_ref/doc/uid/TP40000939-CH1-SW21
					// The metadata item keys atom holds a list of the metadata keys that may be present in the metadata atom.
					// This list is indexed starting with 1; 0 is a reserved index value. The metadata item keys atom is a full atom with an atom type of "keys".
					$atom_structure['version']       = getid3_lib::BigEndian2Int(substr($atom_data,  0, 1));
					$atom_structure['flags_raw']     = getid3_lib::BigEndian2Int(substr($atom_data,  1, 3));
					$atom_structure['entry_count']   = getid3_lib::BigEndian2Int(substr($atom_data,  4, 4));
					$keys_atom_offset = 8;
					for ($i = 1; $i <= $atom_structure['entry_count']; $i++) {
						$atom_structure['keys'][$i]['key_size']      = getid3_lib::BigEndian2Int(substr($atom_data, $keys_atom_offset + 0, 4));
						$atom_structure['keys'][$i]['key_namespace'] =                           substr($atom_data, $keys_atom_offset + 4, 4);
						$atom_structure['keys'][$i]['key_value']     =                           substr($atom_data, $keys_atom_offset + 8, $atom_structure['keys'][$i]['key_size'] - 8);
						$keys_atom_offset += $atom_structure['keys'][$i]['key_size']; // key_size includes the 4+4 bytes for key_size and key_namespace

						$info['quicktime']['temp_meta_key_names'][$i] = $atom_structure['keys'][$i]['key_value'];
					}
					break;

				case 'uuid': // user-defined atom often seen containing XML data, also used for potentially many other purposes, only a few specifically handled by getID3 (e.g. 360fly spatial data)
					//Get the UUID ID in first 16 bytes
					$uuid_bytes_read = unpack('H8time_low/H4time_mid/H4time_hi/H4clock_seq_hi/H12clock_seq_low', substr($atom_data, 0, 16));
					$atom_structure['uuid_field_id'] = implode('-', $uuid_bytes_read);

					switch ($atom_structure['uuid_field_id']) {   // http://fileformats.archiveteam.org/wiki/Boxes/atoms_format#UUID_boxes

						case '0537cdab-9d0c-4431-a72a-fa561f2a113e': // Exif                                       - http://fileformats.archiveteam.org/wiki/Exif
						case '2c4c0100-8504-40b9-a03e-562148d6dfeb': // Photoshop Image Resources                  - http://fileformats.archiveteam.org/wiki/Photoshop_Image_Resources
						case '33c7a4d2-b81d-4723-a0ba-f1a3e097ad38': // IPTC-IIM                                   - http://fileformats.archiveteam.org/wiki/IPTC-IIM
						case '8974dbce-7be7-4c51-84f9-7148f9882554': // PIFF Track Encryption Box                  - http://fileformats.archiveteam.org/wiki/Protected_Interoperable_File_Format
						case '96a9f1f1-dc98-402d-a7ae-d68e34451809': // GeoJP2 World File Box                      - http://fileformats.archiveteam.org/wiki/GeoJP2
						case 'a2394f52-5a9b-4f14-a244-6c427c648df4': // PIFF Sample Encryption Box                 - http://fileformats.archiveteam.org/wiki/Protected_Interoperable_File_Format
						case 'b14bf8bd-083d-4b43-a5ae-8cd7d5a6ce03': // GeoJP2 GeoTIFF Box                         - http://fileformats.archiveteam.org/wiki/GeoJP2
						case 'd08a4f18-10f3-4a82-b6c8-32d8aba183d3': // PIFF Protection System Specific Header Box - http://fileformats.archiveteam.org/wiki/Protected_Interoperable_File_Format
							$this->warning('Unhandled (but recognized) "uuid" atom identified by "'.$atom_structure['uuid_field_id'].'" at offset '.$atom_structure['offset'].' ('.strlen($atom_data).' bytes)');
							break;

						case 'be7acfcb-97a9-42e8-9c71-999491e3afac': // XMP data (in XML format)
							$atom_structure['xml'] = substr($atom_data, 16, strlen($atom_data) - 16 - 8); // 16 bytes for UUID, 8 bytes header(?)
							break;

						case 'efe1589a-bb77-49ef-8095-27759eb1dc6f': // 360fly data
							/* 360fly code in this block by Paul Lewis 2019-Oct-31 */
							/*	Sensor Timestamps need to be calculated using the recordings base time at ['quicktime']['moov']['subatoms'][0]['creation_time_unix']. */
							$atom_structure['title'] = '360Fly Sensor Data';

							//Get the UUID HEADER data
							$uuid_bytes_read = unpack('vheader_size/vheader_version/vtimescale/vhardware_version/x/x/x/x/x/x/x/x/x/x/x/x/x/x/x/x/', substr($atom_data, 16, 32));
							$atom_structure['uuid_header'] = $uuid_bytes_read;

							$start_byte = 48;
							$atom_SENSOR_data = substr($atom_data, $start_byte);
							$atom_structure['sensor_data']['data_type'] = array(
									'fusion_count'   => 0,       // ID 250
									'fusion_data'    => array(),
									'accel_count'    => 0,       // ID 1
									'accel_data'     => array(),
									'gyro_count'     => 0,       // ID 2
									'gyro_data'      => array(),
									'magno_count'    => 0,       // ID 3
									'magno_data'     => array(),
									'gps_count'      => 0,       // ID 5
									'gps_data'       => array(),
									'rotation_count' => 0,       // ID 6
									'rotation_data'  => array(),
									'unknown_count'  => 0,       // ID ??
									'unknown_data'   => array(),
									'debug_list'     => '',      // Used to debug variables stored as comma delimited strings
							);
							$debug_structure = array();
							$debug_structure['debug_items'] = array();
							// Can start loop here to decode all sensor data in 32 Byte chunks:
							foreach (str_split($atom_SENSOR_data, 32) as $sensor_key => $sensor_data) {
								// This gets me a data_type code to work out what data is in the next 31 bytes.
								$sensor_data_type = substr($sensor_data, 0, 1);
								$sensor_data_content = substr($sensor_data, 1);
								$uuid_bytes_read = unpack('C*', $sensor_data_type);
								$sensor_data_array = array();
								switch ($uuid_bytes_read[1]) {
									case 250:
										$atom_structure['sensor_data']['data_type']['fusion_count']++;
										$uuid_bytes_read = unpack('cmode/Jtimestamp/Gyaw/Gpitch/Groll/x*', $sensor_data_content);
										$sensor_data_array['mode']      = $uuid_bytes_read['mode'];
										$sensor_data_array['timestamp'] = $uuid_bytes_read['timestamp'];
										$sensor_data_array['yaw']       = $uuid_bytes_read['yaw'];
										$sensor_data_array['pitch']     = $uuid_bytes_read['pitch'];
										$sensor_data_array['roll']      = $uuid_bytes_read['roll'];
										array_push($atom_structure['sensor_data']['data_type']['fusion_data'], $sensor_data_array);
										break;
									case 1:
										$atom_structure['sensor_data']['data_type']['accel_count']++;
										$uuid_bytes_read = unpack('cmode/Jtimestamp/Gyaw/Gpitch/Groll/x*', $sensor_data_content);
										$sensor_data_array['mode']      = $uuid_bytes_read['mode'];
										$sensor_data_array['timestamp'] = $uuid_bytes_read['timestamp'];
										$sensor_data_array['yaw']       = $uuid_bytes_read['yaw'];
										$sensor_data_array['pitch']     = $uuid_bytes_read['pitch'];
										$sensor_data_array['roll']      = $uuid_bytes_read['roll'];
										array_push($atom_structure['sensor_data']['data_type']['accel_data'], $sensor_data_array);
										break;
									case 2:
										$atom_structure['sensor_data']['data_type']['gyro_count']++;
										$uuid_bytes_read = unpack('cmode/Jtimestamp/Gyaw/Gpitch/Groll/x*', $sensor_data_content);
										$sensor_data_array['mode']      = $uuid_bytes_read['mode'];
										$sensor_data_array['timestamp'] = $uuid_bytes_read['timestamp'];
										$sensor_data_array['yaw']       = $uuid_bytes_read['yaw'];
										$sensor_data_array['pitch']     = $uuid_bytes_read['pitch'];
										$sensor_data_array['roll']      = $uuid_bytes_read['roll'];
										array_push($atom_structure['sensor_data']['data_type']['gyro_data'], $sensor_data_array);
										break;
									case 3:
										$atom_structure['sensor_data']['data_type']['magno_count']++;
										$uuid_bytes_read = unpack('cmode/Jtimestamp/Gmagx/Gmagy/Gmagz/x*', $sensor_data_content);
										$sensor_data_array['mode']      = $uuid_bytes_read['mode'];
										$sensor_data_array['timestamp'] = $uuid_bytes_read['timestamp'];
										$sensor_data_array['magx']      = $uuid_bytes_read['magx'];
										$sensor_data_array['magy']      = $uuid_bytes_read['magy'];
										$sensor_data_array['magz']      = $uuid_bytes_read['magz'];
										array_push($atom_structure['sensor_data']['data_type']['magno_data'], $sensor_data_array);
										break;
									case 5:
										$atom_structure['sensor_data']['data_type']['gps_count']++;
										$uuid_bytes_read = unpack('cmode/Jtimestamp/Glat/Glon/Galt/Gspeed/nbearing/nacc/x*', $sensor_data_content);
										$sensor_data_array['mode']      = $uuid_bytes_read['mode'];
										$sensor_data_array['timestamp'] = $uuid_bytes_read['timestamp'];
										$sensor_data_array['lat']       = $uuid_bytes_read['lat'];
										$sensor_data_array['lon']       = $uuid_bytes_read['lon'];
										$sensor_data_array['alt']       = $uuid_bytes_read['alt'];
										$sensor_data_array['speed']     = $uuid_bytes_read['speed'];
										$sensor_data_array['bearing']   = $uuid_bytes_read['bearing'];
										$sensor_data_array['acc']       = $uuid_bytes_read['acc'];
										array_push($atom_structure['sensor_data']['data_type']['gps_data'], $sensor_data_array);
										//array_push($debug_structure['debug_items'], $uuid_bytes_read['timestamp']);
										break;
									case 6:
										$atom_structure['sensor_data']['data_type']['rotation_count']++;
										$uuid_bytes_read = unpack('cmode/Jtimestamp/Grotx/Groty/Grotz/x*', $sensor_data_content);
										$sensor_data_array['mode']      = $uuid_bytes_read['mode'];
										$sensor_data_array['timestamp'] = $uuid_bytes_read['timestamp'];
										$sensor_data_array['rotx']      = $uuid_bytes_read['rotx'];
										$sensor_data_array['roty']      = $uuid_bytes_read['roty'];
										$sensor_data_array['rotz']      = $uuid_bytes_read['rotz'];
										array_push($atom_structure['sensor_data']['data_type']['rotation_data'], $sensor_data_array);
										break;
									default:
										$atom_structure['sensor_data']['data_type']['unknown_count']++;
										break;
								}
							}
							//if (isset($debug_structure['debug_items']) && count($debug_structure['debug_items']) > 0) {
							//	$atom_structure['sensor_data']['data_type']['debug_list'] = implode(',', $debug_structure['debug_items']);
							//} else {
								$atom_structure['sensor_data']['data_type']['debug_list'] = 'No debug items in list!';
							//}
							break;

						default:
							$this->warning('Unhandled "uuid" atom identified by "'.$atom_structure['uuid_field_id'].'" at offset '.$atom_structure['offset'].' ('.strlen($atom_data).' bytes)');
					}
					break;

				case 'gps ':
					// https://dashcamtalk.com/forum/threads/script-to-extract-gps-data-from-novatek-mp4.20808/page-2#post-291730
					// The 'gps ' contains simple look up table made up of 8byte rows, that point to the 'free' atoms that contains the actual GPS data.
					// The first row is version/metadata/notsure, I skip that.
					// The following rows consist of 4byte address (absolute) and 4byte size (0x1000), these point to the GPS data in the file.

					$GPS_rowsize = 8; // 4 bytes for offset, 4 bytes for size
					if (strlen($atom_data) > 0) {
						if ((strlen($atom_data) % $GPS_rowsize) == 0) {
							$atom_structure['gps_toc'] = array();
							foreach (str_split($atom_data, $GPS_rowsize) as $counter => $datapair) {
								$atom_structure['gps_toc'][] = unpack('Noffset/Nsize', substr($atom_data, $counter * $GPS_rowsize, $GPS_rowsize));
							}

							$atom_structure['gps_entries'] = array();
							$previous_offset = $this->ftell();
							foreach ($atom_structure['gps_toc'] as $key => $gps_pointer) {
								if ($key == 0) {
									// "The first row is version/metadata/notsure, I skip that."
									continue;
								}
								$this->fseek($gps_pointer['offset']);
								$GPS_free_data = $this->fread($gps_pointer['size']);

								/*
								// 2017-05-10: I see some of the data, notably the Hour-Minute-Second, but cannot reconcile the rest of the data. However, the NMEA "GPRMC" line is there and relatively easy to parse, so I'm using that instead

								// https://dashcamtalk.com/forum/threads/script-to-extract-gps-data-from-novatek-mp4.20808/page-2#post-291730
								// The structure of the GPS data atom (the 'free' atoms mentioned above) is following:
								// hour,minute,second,year,month,day,active,latitude_b,longitude_b,unknown2,latitude,longitude,speed = struct.unpack_from('<IIIIIIssssfff',data, 48)
								// For those unfamiliar with python struct:
								// I = int
								// s = is string (size 1, in this case)
								// f = float

								//$atom_structure['gps_entries'][$key] = unpack('Vhour/Vminute/Vsecond/Vyear/Vmonth/Vday/Vactive/Vlatitude_b/Vlongitude_b/Vunknown2/flatitude/flongitude/fspeed', substr($GPS_free_data, 48));
								*/

								// $GPRMC,081836,A,3751.65,S,14507.36,E,000.0,360.0,130998,011.3,E*62
								// $GPRMC,183731,A,3907.482,N,12102.436,W,000.0,360.0,080301,015.5,E*67
								// $GPRMC,002454,A,3553.5295,N,13938.6570,E,0.0,43.1,180700,7.1,W,A*3F
								// $GPRMC,094347.000,A,5342.0061,N,00737.9908,W,0.01,156.75,140217,,,A*7D
								if (preg_match('#\\$GPRMC,([0-9\\.]*),([AV]),([0-9\\.]*),([NS]),([0-9\\.]*),([EW]),([0-9\\.]*),([0-9\\.]*),([0-9]*),([0-9\\.]*),([EW]?)(,[A])?(\\*[0-9A-F]{2})#', $GPS_free_data, $matches)) {
									$GPS_this_GPRMC = array();
									$GPS_this_GPRMC_raw = array();
									list(
										$GPS_this_GPRMC_raw['gprmc'],
										$GPS_this_GPRMC_raw['timestamp'],
										$GPS_this_GPRMC_raw['status'],
										$GPS_this_GPRMC_raw['latitude'],
										$GPS_this_GPRMC_raw['latitude_direction'],
										$GPS_this_GPRMC_raw['longitude'],
										$GPS_this_GPRMC_raw['longitude_direction'],
										$GPS_this_GPRMC_raw['knots'],
										$GPS_this_GPRMC_raw['angle'],
										$GPS_this_GPRMC_raw['datestamp'],
										$GPS_this_GPRMC_raw['variation'],
										$GPS_this_GPRMC_raw['variation_direction'],
										$dummy,
										$GPS_this_GPRMC_raw['checksum'],
									) = $matches;
									$GPS_this_GPRMC['raw'] = $GPS_this_GPRMC_raw;

									$hour   = substr($GPS_this_GPRMC['raw']['timestamp'], 0, 2);
									$minute = substr($GPS_this_GPRMC['raw']['timestamp'], 2, 2);
									$second = substr($GPS_this_GPRMC['raw']['timestamp'], 4, 2);
									$ms     = substr($GPS_this_GPRMC['raw']['timestamp'], 6);    // may contain decimal seconds
									$day    = substr($GPS_this_GPRMC['raw']['datestamp'], 0, 2);
									$month  = substr($GPS_this_GPRMC['raw']['datestamp'], 2, 2);
									$year   = (int) substr($GPS_this_GPRMC['raw']['datestamp'], 4, 2);
									$year += (($year > 90) ? 1900 : 2000); // complete lack of foresight: datestamps are stored with 2-digit years, take best guess
									$GPS_this_GPRMC['timestamp'] = $year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':'.$second.$ms;

									$GPS_this_GPRMC['active'] = ($GPS_this_GPRMC['raw']['status'] == 'A'); // A=Active,V=Void

									foreach (array('latitude','longitude') as $latlon) {
										preg_match('#^([0-9]{1,3})([0-9]{2}\\.[0-9]+)$#', $GPS_this_GPRMC['raw'][$latlon], $matches);
										list($dummy, $deg, $min) = $matches;
										$GPS_this_GPRMC[$latlon] = $deg + ($min / 60);
									}
									$GPS_this_GPRMC['latitude']  *= (($GPS_this_GPRMC['raw']['latitude_direction']  == 'S') ? -1 : 1);
									$GPS_this_GPRMC['longitude'] *= (($GPS_this_GPRMC['raw']['longitude_direction'] == 'W') ? -1 : 1);

									$GPS_this_GPRMC['heading']    = $GPS_this_GPRMC['raw']['angle'];
									$GPS_this_GPRMC['speed_knot'] = $GPS_this_GPRMC['raw']['knots'];
									$GPS_this_GPRMC['speed_kmh']  = $GPS_this_GPRMC['raw']['knots'] * 1.852;
									if ($GPS_this_GPRMC['raw']['variation']) {
										$GPS_this_GPRMC['variation']  = $GPS_this_GPRMC['raw']['variation'];
										$GPS_this_GPRMC['variation'] *= (($GPS_this_GPRMC['raw']['variation_direction'] == 'W') ? -1 : 1);
									}

									$atom_structure['gps_entries'][$key] = $GPS_this_GPRMC;

									@$info['quicktime']['gps_track'][$GPS_this_GPRMC['timestamp']] = array(
										'latitude'  => (float) $GPS_this_GPRMC['latitude'],
										'longitude' => (float) $GPS_this_GPRMC['longitude'],
										'speed_kmh' => (float) $GPS_this_GPRMC['speed_kmh'],
										'heading'   => (float) $GPS_this_GPRMC['heading'],
									);

								} else {
									$this->warning('Unhandled GPS format in "free" atom at offset '.$gps_pointer['offset']);
								}
							}
							$this->fseek($previous_offset);

						} else {
							$this->warning('QuickTime atom "'.$atomname.'" is not mod-8 bytes long ('.$atomsize.' bytes) at offset '.$baseoffset);
						}
					} else {
						$this->warning('QuickTime atom "'.$atomname.'" is zero bytes long at offset '.$baseoffset);
					}
					break;

				case 'loci':// 3GP location (El Loco)
					$loffset = 0;
					$info['quicktime']['comments']['gps_flags']     = array(  getid3_lib::BigEndian2Int(substr($atom_data, 0, 4)));
					$info['quicktime']['comments']['gps_lang']      = array(  getid3_lib::BigEndian2Int(substr($atom_data, 4, 2)));
					$info['quicktime']['comments']['gps_location']  = array(          $this->LociString(substr($atom_data, 6), $loffset));
					$loci_data = substr($atom_data, 6 + $loffset);
					$info['quicktime']['comments']['gps_role']      = array(  getid3_lib::BigEndian2Int(substr($loci_data, 0, 1)));
					$info['quicktime']['comments']['gps_longitude'] = array(getid3_lib::FixedPoint16_16(substr($loci_data, 1, 4)));
					$info['quicktime']['comments']['gps_latitude']  = array(getid3_lib::FixedPoint16_16(substr($loci_data, 5, 4)));
					$info['quicktime']['comments']['gps_altitude']  = array(getid3_lib::FixedPoint16_16(substr($loci_data, 9, 4)));
					$info['quicktime']['comments']['gps_body']      = array(          $this->LociString(substr($loci_data, 13           ), $loffset));
					$info['quicktime']['comments']['gps_notes']     = array(          $this->LociString(substr($loci_data, 13 + $loffset), $loffset));
					break;

				case 'chpl': // CHaPter List
					// https://www.adobe.com/content/dam/Adobe/en/devnet/flv/pdfs/video_file_format_spec_v10.pdf
					$chpl_version = getid3_lib::BigEndian2Int(substr($atom_data, 4, 1)); // Expected to be 0
					$chpl_flags   = getid3_lib::BigEndian2Int(substr($atom_data, 5, 3)); // Reserved, set to 0
					$chpl_count   = getid3_lib::BigEndian2Int(substr($atom_data, 8, 1));
					$chpl_offset = 9;
					for ($i = 0; $i < $chpl_count; $i++) {
						if (($chpl_offset + 9) >= strlen($atom_data)) {
							$this->warning('QuickTime chapter '.$i.' extends beyond end of "chpl" atom');
							break;
						}
						$info['quicktime']['chapters'][$i]['timestamp'] = getid3_lib::BigEndian2Int(substr($atom_data, $chpl_offset, 8)) / 10000000; // timestamps are stored as 100-nanosecond units
						$chpl_offset += 8;
						$chpl_title_size = getid3_lib::BigEndian2Int(substr($atom_data, $chpl_offset, 1));
						$chpl_offset += 1;
						$info['quicktime']['chapters'][$i]['title']     =                           substr($atom_data, $chpl_offset, $chpl_title_size);
						$chpl_offset += $chpl_title_size;
					}
					break;

				case 'FIRM': // FIRMware version(?), seen on GoPro Hero4
					$info['quicktime']['camera']['firmware'] = $atom_data;
					break;

				case 'CAME': // FIRMware version(?), seen on GoPro Hero4
					$info['quicktime']['camera']['serial_hash'] = unpack('H*', $atom_data);
					break;

				case 'dscp':
				case 'rcif':
					// https://www.getid3.org/phpBB3/viewtopic.php?t=1908
					if (substr($atom_data, 0, 7) == "\x00\x00\x00\x00\x55\xC4".'{') {
						if ($json_decoded = @json_decode(rtrim(substr($atom_data, 6), "\x00"), true)) {
							$info['quicktime']['camera'][$atomname] = $json_decoded;
							if (($atomname == 'rcif') && isset($info['quicktime']['camera']['rcif']['wxcamera']['rotate'])) {
								$info['video']['rotate'] = $info['quicktime']['video']['rotate'] = $info['quicktime']['camera']['rcif']['wxcamera']['rotate'];
							}
						} else {
							$this->warning('Failed to JSON decode atom "'.$atomname.'"');
							$atom_structure['data'] = $atom_data;
						}
						unset($json_decoded);
					} else {
						$this->warning('Expecting 55 C4 7B at start of atom "'.$atomname.'", found '.getid3_lib::PrintHexBytes(substr($atom_data, 4, 3)).' instead');
						$atom_structure['data'] = $atom_data;
					}
					break;

				case 'frea':
					// https://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/Kodak.html#frea
					// may contain "scra" (PreviewImage) and/or "thma" (ThumbnailImage)
					$atom_structure['subatoms'] = $this->QuicktimeParseContainerAtom($atom_data, $baseoffset + 4, $atomHierarchy, $ParseAllPossibleAtoms);
					break;
				case 'tima': // subatom to "frea"
					// no idea what this does, the one sample file I've seen has a value of 0x00000027
					$atom_structure['data'] = $atom_data;
					break;
				case 'ver ': // subatom to "frea"
					// some kind of version number, the one sample file I've seen has a value of "3.00.073"
					$atom_structure['data'] = $atom_data;
					break;
				case 'thma': // subatom to "frea" -- "ThumbnailImage"
					// https://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/Kodak.html#frea
					if (strlen($atom_data) > 0) {
						$info['quicktime']['comments']['picture'][] = array('data'=>$atom_data, 'image_mime'=>'image/jpeg', 'description'=>'ThumbnailImage');
					}
					break;
				case 'scra': // subatom to "frea" -- "PreviewImage"
					// https://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/Kodak.html#frea
					// but the only sample file I've seen has no useful data here
					if (strlen($atom_data) > 0) {
						$info['quicktime']['comments']['picture'][] = array('data'=>$atom_data, 'image_mime'=>'image/jpeg', 'description'=>'PreviewImage');
					}
					break;

				case 'cdsc': // timed metadata reference
					// A QuickTime movie can contain none, one, or several timed metadata tracks. Timed metadata tracks can refer to multiple tracks.
					// Metadata tracks are linked to the tracks they describe using a track-reference of type 'cdsc'. The metadata track holds the 'cdsc' track reference.
					$atom_structure['track_number'] = getid3_lib::BigEndian2Int($atom_data);
					break;

				default:
					$this->warning('Unknown QuickTime atom type: "'.preg_replace('#[^a-zA-Z0-9 _\\-]#', '?', $atomname).'" ('.trim(getid3_lib::PrintHexBytes($atomname)).'), '.$atomsize.' bytes at offset '.$baseoffset);
					$atom_structure['data'] = $atom_data;
					break;
			}
		}
		array_pop($atomHierarchy);
		return $atom_structure;
	}

	/**
	 * @param string $atom_data
	 * @param int    $baseoffset
	 * @param array  $atomHierarchy
	 * @param bool   $ParseAllPossibleAtoms
	 *
	 * @return array|false
	 */
	public function QuicktimeParseContainerAtom($atom_data, $baseoffset, &$atomHierarchy, $ParseAllPossibleAtoms) {
		$atom_structure = array();
		$subatomoffset  = 0;
		$subatomcounter = 0;
		if ((strlen($atom_data) == 4) && (getid3_lib::BigEndian2Int($atom_data) == 0x00000000)) {
			return false;
		}
		while ($subatomoffset < strlen($atom_data)) {
			$subatomsize = getid3_lib::BigEndian2Int(substr($atom_data, $subatomoffset + 0, 4));
			$subatomname =                           substr($atom_data, $subatomoffset + 4, 4);
			$subatomdata =                           substr($atom_data, $subatomoffset + 8, $subatomsize - 8);
			if ($subatomsize == 0) {
				// Furthermore, for historical reasons the list of atoms is optionally
				// terminated by a 32-bit integer set to 0. If you are writing a program
				// to read user data atoms, you should allow for the terminating 0.
				if (strlen($atom_data) > 12) {
					$subatomoffset += 4;
					continue;
				}
				break;
			}
			if (strlen($subatomdata) < ($subatomsize - 8)) {
			    // we don't have enough data to decode the subatom.
			    // this may be because we are refusing to parse large subatoms, or it may be because this atom had its size set too large
			    // so we passed in the start of a following atom incorrectly?
			    break;
			}
			$atom_structure[$subatomcounter++] = $this->QuicktimeParseAtom($subatomname, $subatomsize, $subatomdata, $baseoffset + $subatomoffset, $atomHierarchy, $ParseAllPossibleAtoms);
			$subatomoffset += $subatomsize;
		}

		if (empty($atom_structure)) {
			return false;
		}

		return $atom_structure;
	}

	/**
	 * @param string $data
	 * @param int    $offset
	 *
	 * @return int
	 */
	public function quicktime_read_mp4_descr_length($data, &$offset) {
		// http://libquicktime.sourcearchive.com/documentation/2:1.0.2plus-pdebian-2build1/esds_8c-source.html
		$num_bytes = 0;
		$length    = 0;
		do {
			$b = ord(substr($data, $offset++, 1));
			$length = ($length << 7) | ($b & 0x7F);
		} while (($b & 0x80) && ($num_bytes++ < 4));
		return $length;
	}

	/**
	 * @param int $languageid
	 *
	 * @return string
	 */
	public function QuicktimeLanguageLookup($languageid) {
		// http://developer.apple.com/library/mac/#documentation/QuickTime/QTFF/QTFFChap4/qtff4.html#//apple_ref/doc/uid/TP40000939-CH206-34353
		static $QuicktimeLanguageLookup = array();
		if (empty($QuicktimeLanguageLookup)) {
			$QuicktimeLanguageLookup[0]     = 'English';
			$QuicktimeLanguageLookup[1]     = 'French';
			$QuicktimeLanguageLookup[2]     = 'German';
			$QuicktimeLanguageLookup[3]     = 'Italian';
			$QuicktimeLanguageLookup[4]     = 'Dutch';
			$QuicktimeLanguageLookup[5]     = 'Swedish';
			$QuicktimeLanguageLookup[6]     = 'Spanish';
			$QuicktimeLanguageLookup[7]     = 'Danish';
			$QuicktimeLanguageLookup[8]     = 'Portuguese';
			$QuicktimeLanguageLookup[9]     = 'Norwegian';
			$QuicktimeLanguageLookup[10]    = 'Hebrew';
			$QuicktimeLanguageLookup[11]    = 'Japanese';
			$QuicktimeLanguageLookup[12]    = 'Arabic';
			$QuicktimeLanguageLookup[13]    = 'Finnish';
			$QuicktimeLanguageLookup[14]    = 'Greek';
			$QuicktimeLanguageLookup[15]    = 'Icelandic';
			$QuicktimeLanguageLookup[16]    = 'Maltese';
			$QuicktimeLanguageLookup[17]    = 'Turkish';
			$QuicktimeLanguageLookup[18]    = 'Croatian';
			$QuicktimeLanguageLookup[19]    = 'Chinese (Traditional)';
			$QuicktimeLanguageLookup[20]    = 'Urdu';
			$QuicktimeLanguageLookup[21]    = 'Hindi';
			$QuicktimeLanguageLookup[22]    = 'Thai';
			$QuicktimeLanguageLookup[23]    = 'Korean';
			$QuicktimeLanguageLookup[24]    = 'Lithuanian';
			$QuicktimeLanguageLookup[25]    = 'Polish';
			$QuicktimeLanguageLookup[26]    = 'Hungarian';
			$QuicktimeLanguageLookup[27]    = 'Estonian';
			$QuicktimeLanguageLookup[28]    = 'Lettish';
			$QuicktimeLanguageLookup[28]    = 'Latvian';
			$QuicktimeLanguageLookup[29]    = 'Saamisk';
			$QuicktimeLanguageLookup[29]    = 'Lappish';
			$QuicktimeLanguageLookup[30]    = 'Faeroese';
			$QuicktimeLanguageLookup[31]    = 'Farsi';
			$QuicktimeLanguageLookup[31]    = 'Persian';
			$QuicktimeLanguageLookup[32]    = 'Russian';
			$QuicktimeLanguageLookup[33]    = 'Chinese (Simplified)';
			$QuicktimeLanguageLookup[34]    = 'Flemish';
			$QuicktimeLanguageLookup[35]    = 'Irish';
			$QuicktimeLanguageLookup[36]    = 'Albanian';
			$QuicktimeLanguageLookup[37]    = 'Romanian';
			$QuicktimeLanguageLookup[38]    = 'Czech';
			$QuicktimeLanguageLookup[39]    = 'Slovak';
			$QuicktimeLanguageLookup[40]    = 'Slovenian';
			$QuicktimeLanguageLookup[41]    = 'Yiddish';
			$QuicktimeLanguageLookup[42]    = 'Serbian';
			$QuicktimeLanguageLookup[43]    = 'Macedonian';
			$QuicktimeLanguageLookup[44]    = 'Bulgarian';
			$QuicktimeLanguageLookup[45]    = 'Ukrainian';
			$QuicktimeLanguageLookup[46]    = 'Byelorussian';
			$QuicktimeLanguageLookup[47]    = 'Uzbek';
			$QuicktimeLanguageLookup[48]    = 'Kazakh';
			$QuicktimeLanguageLookup[49]    = 'Azerbaijani';
			$QuicktimeLanguageLookup[50]    = 'AzerbaijanAr';
			$QuicktimeLanguageLookup[51]    = 'Armenian';
			$QuicktimeLanguageLookup[52]    = 'Georgian';
			$QuicktimeLanguageLookup[53]    = 'Moldavian';
			$QuicktimeLanguageLookup[54]    = 'Kirghiz';
			$QuicktimeLanguageLookup[55]    = 'Tajiki';
			$QuicktimeLanguageLookup[56]    = 'Turkmen';
			$QuicktimeLanguageLookup[57]    = 'Mongolian';
			$QuicktimeLanguageLookup[58]    = 'MongolianCyr';
			$QuicktimeLanguageLookup[59]    = 'Pashto';
			$QuicktimeLanguageLookup[60]    = 'Kurdish';
			$QuicktimeLanguageLookup[61]    = 'Kashmiri';
			$QuicktimeLanguageLookup[62]    = 'Sindhi';
			$QuicktimeLanguageLookup[63]    = 'Tibetan';
			$QuicktimeLanguageLookup[64]    = 'Nepali';
			$QuicktimeLanguageLookup[65]    = 'Sanskrit';
			$QuicktimeLanguageLookup[66]    = 'Marathi';
			$QuicktimeLanguageLookup[67]    = 'Bengali';
			$QuicktimeLanguageLookup[68]    = 'Assamese';
			$QuicktimeLanguageLookup[69]    = 'Gujarati';
			$QuicktimeLanguageLookup[70]    = 'Punjabi';
			$QuicktimeLanguageLookup[71]    = 'Oriya';
			$QuicktimeLanguageLookup[72]    = 'Malayalam';
			$QuicktimeLanguageLookup[73]    = 'Kannada';
			$QuicktimeLanguageLookup[74]    = 'Tamil';
			$QuicktimeLanguageLookup[75]    = 'Telugu';
			$QuicktimeLanguageLookup[76]    = 'Sinhalese';
			$QuicktimeLanguageLookup[77]    = 'Burmese';
			$QuicktimeLanguageLookup[78]    = 'Khmer';
			$QuicktimeLanguageLookup[79]    = 'Lao';
			$QuicktimeLanguageLookup[80]    = 'Vietnamese';
			$QuicktimeLanguageLookup[81]    = 'Indonesian';
			$QuicktimeLanguageLookup[82]    = 'Tagalog';
			$QuicktimeLanguageLookup[83]    = 'MalayRoman';
			$QuicktimeLanguageLookup[84]    = 'MalayArabic';
			$QuicktimeLanguageLookup[85]    = 'Amharic';
			$QuicktimeLanguageLookup[86]    = 'Tigrinya';
			$QuicktimeLanguageLookup[87]    = 'Galla';
			$QuicktimeLanguageLookup[87]    = 'Oromo';
			$QuicktimeLanguageLookup[88]    = 'Somali';
			$QuicktimeLanguageLookup[89]    = 'Swahili';
			$QuicktimeLanguageLookup[90]    = 'Ruanda';
			$QuicktimeLanguageLookup[91]    = 'Rundi';
			$QuicktimeLanguageLookup[92]    = 'Chewa';
			$QuicktimeLanguageLookup[93]    = 'Malagasy';
			$QuicktimeLanguageLookup[94]    = 'Esperanto';
			$QuicktimeLanguageLookup[128]   = 'Welsh';
			$QuicktimeLanguageLookup[129]   = 'Basque';
			$QuicktimeLanguageLookup[130]   = 'Catalan';
			$QuicktimeLanguageLookup[131]   = 'Latin';
			$QuicktimeLanguageLookup[132]   = 'Quechua';
			$QuicktimeLanguageLookup[133]   = 'Guarani';
			$QuicktimeLanguageLookup[134]   = 'Aymara';
			$QuicktimeLanguageLookup[135]   = 'Tatar';
			$QuicktimeLanguageLookup[136]   = 'Uighur';
			$QuicktimeLanguageLookup[137]   = 'Dzongkha';
			$QuicktimeLanguageLookup[138]   = 'JavaneseRom';
			$QuicktimeLanguageLookup[32767] = 'Unspecified';
		}
		if (($languageid > 138) && ($languageid < 32767)) {
			/*
			ISO Language Codes - http://www.loc.gov/standards/iso639-2/php/code_list.php
			Because the language codes specified by ISO 639-2/T are three characters long, they must be packed to fit into a 16-bit field.
			The packing algorithm must map each of the three characters, which are always lowercase, into a 5-bit integer and then concatenate
			these integers into the least significant 15 bits of a 16-bit integer, leaving the 16-bit integer's most significant bit set to zero.

			One algorithm for performing this packing is to treat each ISO character as a 16-bit integer. Subtract 0x60 from the first character
			and multiply by 2^10 (0x400), subtract 0x60 from the second character and multiply by 2^5 (0x20), subtract 0x60 from the third character,
			and add the three 16-bit values. This will result in a single 16-bit value with the three codes correctly packed into the 15 least
			significant bits and the most significant bit set to zero.
			*/
			$iso_language_id  = '';
			$iso_language_id .= chr((($languageid & 0x7C00) >> 10) + 0x60);
			$iso_language_id .= chr((($languageid & 0x03E0) >>  5) + 0x60);
			$iso_language_id .= chr((($languageid & 0x001F) >>  0) + 0x60);
			$QuicktimeLanguageLookup[$languageid] = getid3_id3v2::LanguageLookup($iso_language_id);
		}
		return (isset($QuicktimeLanguageLookup[$languageid]) ? $QuicktimeLanguageLookup[$languageid] : 'invalid');
	}

	/**
	 * @param string $codecid
	 *
	 * @return string
	 */
	public function QuicktimeVideoCodecLookup($codecid) {
		static $QuicktimeVideoCodecLookup = array();
		if (empty($QuicktimeVideoCodecLookup)) {
			$QuicktimeVideoCodecLookup['.SGI'] = 'SGI';
			$QuicktimeVideoCodecLookup['3IV1'] = '3ivx MPEG-4 v1';
			$QuicktimeVideoCodecLookup['3IV2'] = '3ivx MPEG-4 v2';
			$QuicktimeVideoCodecLookup['3IVX'] = '3ivx MPEG-4';
			$QuicktimeVideoCodecLookup['8BPS'] = 'Planar RGB';
			$QuicktimeVideoCodecLookup['avc1'] = 'H.264/MPEG-4 AVC';
			$QuicktimeVideoCodecLookup['avr '] = 'AVR-JPEG';
			$QuicktimeVideoCodecLookup['b16g'] = '16Gray';
			$QuicktimeVideoCodecLookup['b32a'] = '32AlphaGray';
			$QuicktimeVideoCodecLookup['b48r'] = '48RGB';
			$QuicktimeVideoCodecLookup['b64a'] = '64ARGB';
			$QuicktimeVideoCodecLookup['base'] = 'Base';
			$QuicktimeVideoCodecLookup['clou'] = 'Cloud';
			$QuicktimeVideoCodecLookup['cmyk'] = 'CMYK';
			$QuicktimeVideoCodecLookup['cvid'] = 'Cinepak';
			$QuicktimeVideoCodecLookup['dmb1'] = 'OpenDML JPEG';
			$QuicktimeVideoCodecLookup['dvc '] = 'DVC-NTSC';
			$QuicktimeVideoCodecLookup['dvcp'] = 'DVC-PAL';
			$QuicktimeVideoCodecLookup['dvpn'] = 'DVCPro-NTSC';
			$QuicktimeVideoCodecLookup['dvpp'] = 'DVCPro-PAL';
			$QuicktimeVideoCodecLookup['fire'] = 'Fire';
			$QuicktimeVideoCodecLookup['flic'] = 'FLC';
			$QuicktimeVideoCodecLookup['gif '] = 'GIF';
			$QuicktimeVideoCodecLookup['h261'] = 'H261';
			$QuicktimeVideoCodecLookup['h263'] = 'H263';
			$QuicktimeVideoCodecLookup['IV41'] = 'Indeo4';
			$QuicktimeVideoCodecLookup['jpeg'] = 'JPEG';
			$QuicktimeVideoCodecLookup['kpcd'] = 'PhotoCD';
			$QuicktimeVideoCodecLookup['mjpa'] = 'Motion JPEG-A';
			$QuicktimeVideoCodecLookup['mjpb'] = 'Motion JPEG-B';
			$QuicktimeVideoCodecLookup['msvc'] = 'Microsoft Video1';
			$QuicktimeVideoCodecLookup['myuv'] = 'MPEG YUV420';
			$QuicktimeVideoCodecLookup['path'] = 'Vector';
			$QuicktimeVideoCodecLookup['png '] = 'PNG';
			$QuicktimeVideoCodecLookup['PNTG'] = 'MacPaint';
			$QuicktimeVideoCodecLookup['qdgx'] = 'QuickDrawGX';
			$QuicktimeVideoCodecLookup['qdrw'] = 'QuickDraw';
			$QuicktimeVideoCodecLookup['raw '] = 'RAW';
			$QuicktimeVideoCodecLookup['ripl'] = 'WaterRipple';
			$QuicktimeVideoCodecLookup['rpza'] = 'Video';
			$QuicktimeVideoCodecLookup['smc '] = 'Graphics';
			$QuicktimeVideoCodecLookup['SVQ1'] = 'Sorenson Video 1';
			$QuicktimeVideoCodecLookup['SVQ1'] = 'Sorenson Video 3';
			$QuicktimeVideoCodecLookup['syv9'] = 'Sorenson YUV9';
			$QuicktimeVideoCodecLookup['tga '] = 'Targa';
			$QuicktimeVideoCodecLookup['tiff'] = 'TIFF';
			$QuicktimeVideoCodecLookup['WRAW'] = 'Windows RAW';
			$QuicktimeVideoCodecLookup['WRLE'] = 'BMP';
			$QuicktimeVideoCodecLookup['y420'] = 'YUV420';
			$QuicktimeVideoCodecLookup['yuv2'] = 'ComponentVideo';
			$QuicktimeVideoCodecLookup['yuvs'] = 'ComponentVideoUnsigned';
			$QuicktimeVideoCodecLookup['yuvu'] = 'ComponentVideoSigned';
		}
		return (isset($QuicktimeVideoCodecLookup[$codecid]) ? $QuicktimeVideoCodecLookup[$codecid] : '');
	}

	/**
	 * @param string $codecid
	 *
	 * @return mixed|string
	 */
	public function QuicktimeAudioCodecLookup($codecid) {
		static $QuicktimeAudioCodecLookup = array();
		if (empty($QuicktimeAudioCodecLookup)) {
			$QuicktimeAudioCodecLookup['.mp3']          = 'Fraunhofer MPEG Layer-III alias';
			$QuicktimeAudioCodecLookup['aac ']          = 'ISO/IEC 14496-3 AAC';
			$QuicktimeAudioCodecLookup['agsm']          = 'Apple GSM 10:1';
			$QuicktimeAudioCodecLookup['alac']          = 'Apple Lossless Audio Codec';
			$QuicktimeAudioCodecLookup['alaw']          = 'A-law 2:1';
			$QuicktimeAudioCodecLookup['conv']          = 'Sample Format';
			$QuicktimeAudioCodecLookup['dvca']          = 'DV';
			$QuicktimeAudioCodecLookup['dvi ']          = 'DV 4:1';
			$QuicktimeAudioCodecLookup['eqal']          = 'Frequency Equalizer';
			$QuicktimeAudioCodecLookup['fl32']          = '32-bit Floating Point';
			$QuicktimeAudioCodecLookup['fl64']          = '64-bit Floating Point';
			$QuicktimeAudioCodecLookup['ima4']          = 'Interactive Multimedia Association 4:1';
			$QuicktimeAudioCodecLookup['in24']          = '24-bit Integer';
			$QuicktimeAudioCodecLookup['in32']          = '32-bit Integer';
			$QuicktimeAudioCodecLookup['lpc ']          = 'LPC 23:1';
			$QuicktimeAudioCodecLookup['MAC3']          = 'Macintosh Audio Compression/Expansion (MACE) 3:1';
			$QuicktimeAudioCodecLookup['MAC6']          = 'Macintosh Audio Compression/Expansion (MACE) 6:1';
			$QuicktimeAudioCodecLookup['mixb']          = '8-bit Mixer';
			$QuicktimeAudioCodecLookup['mixw']          = '16-bit Mixer';
			$QuicktimeAudioCodecLookup['mp4a']          = 'ISO/IEC 14496-3 AAC';
			$QuicktimeAudioCodecLookup['MS'."\x00\x02"] = 'Microsoft ADPCM';
			$QuicktimeAudioCodecLookup['MS'."\x00\x11"] = 'DV IMA';
			$QuicktimeAudioCodecLookup['MS'."\x00\x55"] = 'Fraunhofer MPEG Layer III';
			$QuicktimeAudioCodecLookup['NONE']          = 'No Encoding';
			$QuicktimeAudioCodecLookup['Qclp']          = 'Qualcomm PureVoice';
			$QuicktimeAudioCodecLookup['QDM2']          = 'QDesign Music 2';
			$QuicktimeAudioCodecLookup['QDMC']          = 'QDesign Music 1';
			$QuicktimeAudioCodecLookup['ratb']          = '8-bit Rate';
			$QuicktimeAudioCodecLookup['ratw']          = '16-bit Rate';
			$QuicktimeAudioCodecLookup['raw ']          = 'raw PCM';
			$QuicktimeAudioCodecLookup['sour']          = 'Sound Source';
			$QuicktimeAudioCodecLookup['sowt']          = 'signed/two\'s complement (Little Endian)';
			$QuicktimeAudioCodecLookup['str1']          = 'Iomega MPEG layer II';
			$QuicktimeAudioCodecLookup['str2']          = 'Iomega MPEG *layer II';
			$QuicktimeAudioCodecLookup['str3']          = 'Iomega MPEG **layer II';
			$QuicktimeAudioCodecLookup['str4']          = 'Iomega MPEG ***layer II';
			$QuicktimeAudioCodecLookup['twos']          = 'signed/two\'s complement (Big Endian)';
			$QuicktimeAudioCodecLookup['ulaw']          = 'mu-law 2:1';
		}
		return (isset($QuicktimeAudioCodecLookup[$codecid]) ? $QuicktimeAudioCodecLookup[$codecid] : '');
	}

	/**
	 * @param string $compressionid
	 *
	 * @return string
	 */
	public function QuicktimeDCOMLookup($compressionid) {
		static $QuicktimeDCOMLookup = array();
		if (empty($QuicktimeDCOMLookup)) {
			$QuicktimeDCOMLookup['zlib'] = 'ZLib Deflate';
			$QuicktimeDCOMLookup['adec'] = 'Apple Compression';
		}
		return (isset($QuicktimeDCOMLookup[$compressionid]) ? $QuicktimeDCOMLookup[$compressionid] : '');
	}

	/**
	 * @param int $colordepthid
	 *
	 * @return string
	 */
	public function QuicktimeColorNameLookup($colordepthid) {
		static $QuicktimeColorNameLookup = array();
		if (empty($QuicktimeColorNameLookup)) {
			$QuicktimeColorNameLookup[1]  = '2-color (monochrome)';
			$QuicktimeColorNameLookup[2]  = '4-color';
			$QuicktimeColorNameLookup[4]  = '16-color';
			$QuicktimeColorNameLookup[8]  = '256-color';
			$QuicktimeColorNameLookup[16] = 'thousands (16-bit color)';
			$QuicktimeColorNameLookup[24] = 'millions (24-bit color)';
			$QuicktimeColorNameLookup[32] = 'millions+ (32-bit color)';
			$QuicktimeColorNameLookup[33] = 'black & white';
			$QuicktimeColorNameLookup[34] = '4-gray';
			$QuicktimeColorNameLookup[36] = '16-gray';
			$QuicktimeColorNameLookup[40] = '256-gray';
		}
		return (isset($QuicktimeColorNameLookup[$colordepthid]) ? $QuicktimeColorNameLookup[$colordepthid] : 'invalid');
	}

	/**
	 * @param int $stik
	 *
	 * @return string
	 */
	public function QuicktimeSTIKLookup($stik) {
		static $QuicktimeSTIKLookup = array();
		if (empty($QuicktimeSTIKLookup)) {
			$QuicktimeSTIKLookup[0]  = 'Movie';
			$QuicktimeSTIKLookup[1]  = 'Normal';
			$QuicktimeSTIKLookup[2]  = 'Audiobook';
			$QuicktimeSTIKLookup[5]  = 'Whacked Bookmark';
			$QuicktimeSTIKLookup[6]  = 'Music Video';
			$QuicktimeSTIKLookup[9]  = 'Short Film';
			$QuicktimeSTIKLookup[10] = 'TV Show';
			$QuicktimeSTIKLookup[11] = 'Booklet';
			$QuicktimeSTIKLookup[14] = 'Ringtone';
			$QuicktimeSTIKLookup[21] = 'Podcast';
		}
		return (isset($QuicktimeSTIKLookup[$stik]) ? $QuicktimeSTIKLookup[$stik] : 'invalid');
	}

	/**
	 * @param int $audio_profile_id
	 *
	 * @return string
	 */
	public function QuicktimeIODSaudioProfileName($audio_profile_id) {
		static $QuicktimeIODSaudioProfileNameLookup = array();
		if (empty($QuicktimeIODSaudioProfileNameLookup)) {
			$QuicktimeIODSaudioProfileNameLookup = array(
				0x00 => 'ISO Reserved (0x00)',
				0x01 => 'Main Audio Profile @ Level 1',
				0x02 => 'Main Audio Profile @ Level 2',
				0x03 => 'Main Audio Profile @ Level 3',
				0x04 => 'Main Audio Profile @ Level 4',
				0x05 => 'Scalable Audio Profile @ Level 1',
				0x06 => 'Scalable Audio Profile @ Level 2',
				0x07 => 'Scalable Audio Profile @ Level 3',
				0x08 => 'Scalable Audio Profile @ Level 4',
				0x09 => 'Speech Audio Profile @ Level 1',
				0x0A => 'Speech Audio Profile @ Level 2',
				0x0B => 'Synthetic Audio Profile @ Level 1',
				0x0C => 'Synthetic Audio Profile @ Level 2',
				0x0D => 'Synthetic Audio Profile @ Level 3',
				0x0E => 'High Quality Audio Profile @ Level 1',
				0x0F => 'High Quality Audio Profile @ Level 2',
				0x10 => 'High Quality Audio Profile @ Level 3',
				0x11 => 'High Quality Audio Profile @ Level 4',
				0x12 => 'High Quality Audio Profile @ Level 5',
				0x13 => 'High Quality Audio Profile @ Level 6',
				0x14 => 'High Quality Audio Profile @ Level 7',
				0x15 => 'High Quality Audio Profile @ Level 8',
				0x16 => 'Low Delay Audio Profile @ Level 1',
				0x17 => 'Low Delay Audio Profile @ Level 2',
				0x18 => 'Low Delay Audio Profile @ Level 3',
				0x19 => 'Low Delay Audio Profile @ Level 4',
				0x1A => 'Low Delay Audio Profile @ Level 5',
				0x1B => 'Low Delay Audio Profile @ Level 6',
				0x1C => 'Low Delay Audio Profile @ Level 7',
				0x1D => 'Low Delay Audio Profile @ Level 8',
				0x1E => 'Natural Audio Profile @ Level 1',
				0x1F => 'Natural Audio Profile @ Level 2',
				0x20 => 'Natural Audio Profile @ Level 3',
				0x21 => 'Natural Audio Profile @ Level 4',
				0x22 => 'Mobile Audio Internetworking Profile @ Level 1',
				0x23 => 'Mobile Audio Internetworking Profile @ Level 2',
				0x24 => 'Mobile Audio Internetworking Profile @ Level 3',
				0x25 => 'Mobile Audio Internetworking Profile @ Level 4',
				0x26 => 'Mobile Audio Internetworking Profile @ Level 5',
				0x27 => 'Mobile Audio Internetworking Profile @ Level 6',
				0x28 => 'AAC Profile @ Level 1',
				0x29 => 'AAC Profile @ Level 2',
				0x2A => 'AAC Profile @ Level 4',
				0x2B => 'AAC Profile @ Level 5',
				0x2C => 'High Efficiency AAC Profile @ Level 2',
				0x2D => 'High Efficiency AAC Profile @ Level 3',
				0x2E => 'High Efficiency AAC Profile @ Level 4',
				0x2F => 'High Efficiency AAC Profile @ Level 5',
				0xFE => 'Not part of MPEG-4 audio profiles',
				0xFF => 'No audio capability required',
			);
		}
		return (isset($QuicktimeIODSaudioProfileNameLookup[$audio_profile_id]) ? $QuicktimeIODSaudioProfileNameLookup[$audio_profile_id] : 'ISO Reserved / User Private');
	}

	/**
	 * @param int $video_profile_id
	 *
	 * @return string
	 */
	public function QuicktimeIODSvideoProfileName($video_profile_id) {
		static $QuicktimeIODSvideoProfileNameLookup = array();
		if (empty($QuicktimeIODSvideoProfileNameLookup)) {
			$QuicktimeIODSvideoProfileNameLookup = array(
				0x00 => 'Reserved (0x00) Profile',
				0x01 => 'Simple Profile @ Level 1',
				0x02 => 'Simple Profile @ Level 2',
				0x03 => 'Simple Profile @ Level 3',
				0x08 => 'Simple Profile @ Level 0',
				0x10 => 'Simple Scalable Profile @ Level 0',
				0x11 => 'Simple Scalable Profile @ Level 1',
				0x12 => 'Simple Scalable Profile @ Level 2',
				0x15 => 'AVC/H264 Profile',
				0x21 => 'Core Profile @ Level 1',
				0x22 => 'Core Profile @ Level 2',
				0x32 => 'Main Profile @ Level 2',
				0x33 => 'Main Profile @ Level 3',
				0x34 => 'Main Profile @ Level 4',
				0x42 => 'N-bit Profile @ Level 2',
				0x51 => 'Scalable Texture Profile @ Level 1',
				0x61 => 'Simple Face Animation Profile @ Level 1',
				0x62 => 'Simple Face Animation Profile @ Level 2',
				0x63 => 'Simple FBA Profile @ Level 1',
				0x64 => 'Simple FBA Profile @ Level 2',
				0x71 => 'Basic Animated Texture Profile @ Level 1',
				0x72 => 'Basic Animated Texture Profile @ Level 2',
				0x81 => 'Hybrid Profile @ Level 1',
				0x82 => 'Hybrid Profile @ Level 2',
				0x91 => 'Advanced Real Time Simple Profile @ Level 1',
				0x92 => 'Advanced Real Time Simple Profile @ Level 2',
				0x93 => 'Advanced Real Time Simple Profile @ Level 3',
				0x94 => 'Advanced Real Time Simple Profile @ Level 4',
				0xA1 => 'Core Scalable Profile @ Level1',
				0xA2 => 'Core Scalable Profile @ Level2',
				0xA3 => 'Core Scalable Profile @ Level3',
				0xB1 => 'Advanced Coding Efficiency Profile @ Level 1',
				0xB2 => 'Advanced Coding Efficiency Profile @ Level 2',
				0xB3 => 'Advanced Coding Efficiency Profile @ Level 3',
				0xB4 => 'Advanced Coding Efficiency Profile @ Level 4',
				0xC1 => 'Advanced Core Profile @ Level 1',
				0xC2 => 'Advanced Core Profile @ Level 2',
				0xD1 => 'Advanced Scalable Texture @ Level1',
				0xD2 => 'Advanced Scalable Texture @ Level2',
				0xE1 => 'Simple Studio Profile @ Level 1',
				0xE2 => 'Simple Studio Profile @ Level 2',
				0xE3 => 'Simple Studio Profile @ Level 3',
				0xE4 => 'Simple Studio Profile @ Level 4',
				0xE5 => 'Core Studio Profile @ Level 1',
				0xE6 => 'Core Studio Profile @ Level 2',
				0xE7 => 'Core Studio Profile @ Level 3',
				0xE8 => 'Core Studio Profile @ Level 4',
				0xF0 => 'Advanced Simple Profile @ Level 0',
				0xF1 => 'Advanced Simple Profile @ Level 1',
				0xF2 => 'Advanced Simple Profile @ Level 2',
				0xF3 => 'Advanced Simple Profile @ Level 3',
				0xF4 => 'Advanced Simple Profile @ Level 4',
				0xF5 => 'Advanced Simple Profile @ Level 5',
				0xF7 => 'Advanced Simple Profile @ Level 3b',
				0xF8 => 'Fine Granularity Scalable Profile @ Level 0',
				0xF9 => 'Fine Granularity Scalable Profile @ Level 1',
				0xFA => 'Fine Granularity Scalable Profile @ Level 2',
				0xFB => 'Fine Granularity Scalable Profile @ Level 3',
				0xFC => 'Fine Granularity Scalable Profile @ Level 4',
				0xFD => 'Fine Granularity Scalable Profile @ Level 5',
				0xFE => 'Not part of MPEG-4 Visual profiles',
				0xFF => 'No visual capability required',
			);
		}
		return (isset($QuicktimeIODSvideoProfileNameLookup[$video_profile_id]) ? $QuicktimeIODSvideoProfileNameLookup[$video_profile_id] : 'ISO Reserved Profile');
	}

	/**
	 * @param int $rtng
	 *
	 * @return string
	 */
	public function QuicktimeContentRatingLookup($rtng) {
		static $QuicktimeContentRatingLookup = array();
		if (empty($QuicktimeContentRatingLookup)) {
			$QuicktimeContentRatingLookup[0]  = 'None';
			$QuicktimeContentRatingLookup[1]  = 'Explicit';
			$QuicktimeContentRatingLookup[2]  = 'Clean';
			$QuicktimeContentRatingLookup[4]  = 'Explicit (old)';
		}
		return (isset($QuicktimeContentRatingLookup[$rtng]) ? $QuicktimeContentRatingLookup[$rtng] : 'invalid');
	}

	/**
	 * @param int $akid
	 *
	 * @return string
	 */
	public function QuicktimeStoreAccountTypeLookup($akid) {
		static $QuicktimeStoreAccountTypeLookup = array();
		if (empty($QuicktimeStoreAccountTypeLookup)) {
			$QuicktimeStoreAccountTypeLookup[0] = 'iTunes';
			$QuicktimeStoreAccountTypeLookup[1] = 'AOL';
		}
		return (isset($QuicktimeStoreAccountTypeLookup[$akid]) ? $QuicktimeStoreAccountTypeLookup[$akid] : 'invalid');
	}

	/**
	 * @param int $sfid
	 *
	 * @return string
	 */
	public function QuicktimeStoreFrontCodeLookup($sfid) {
		static $QuicktimeStoreFrontCodeLookup = array();
		if (empty($QuicktimeStoreFrontCodeLookup)) {
			$QuicktimeStoreFrontCodeLookup[143460] = 'Australia';
			$QuicktimeStoreFrontCodeLookup[143445] = 'Austria';
			$QuicktimeStoreFrontCodeLookup[143446] = 'Belgium';
			$QuicktimeStoreFrontCodeLookup[143455] = 'Canada';
			$QuicktimeStoreFrontCodeLookup[143458] = 'Denmark';
			$QuicktimeStoreFrontCodeLookup[143447] = 'Finland';
			$QuicktimeStoreFrontCodeLookup[143442] = 'France';
			$QuicktimeStoreFrontCodeLookup[143443] = 'Germany';
			$QuicktimeStoreFrontCodeLookup[143448] = 'Greece';
			$QuicktimeStoreFrontCodeLookup[143449] = 'Ireland';
			$QuicktimeStoreFrontCodeLookup[143450] = 'Italy';
			$QuicktimeStoreFrontCodeLookup[143462] = 'Japan';
			$QuicktimeStoreFrontCodeLookup[143451] = 'Luxembourg';
			$QuicktimeStoreFrontCodeLookup[143452] = 'Netherlands';
			$QuicktimeStoreFrontCodeLookup[143461] = 'New Zealand';
			$QuicktimeStoreFrontCodeLookup[143457] = 'Norway';
			$QuicktimeStoreFrontCodeLookup[143453] = 'Portugal';
			$QuicktimeStoreFrontCodeLookup[143454] = 'Spain';
			$QuicktimeStoreFrontCodeLookup[143456] = 'Sweden';
			$QuicktimeStoreFrontCodeLookup[143459] = 'Switzerland';
			$QuicktimeStoreFrontCodeLookup[143444] = 'United Kingdom';
			$QuicktimeStoreFrontCodeLookup[143441] = 'United States';
		}
		return (isset($QuicktimeStoreFrontCodeLookup[$sfid]) ? $QuicktimeStoreFrontCodeLookup[$sfid] : 'invalid');
	}

	/**
	 * @param string $keyname
	 * @param string|array $data
	 * @param string $boxname
	 *
	 * @return bool
	 */
	public function CopyToAppropriateCommentsSection($keyname, $data, $boxname='') {
		static $handyatomtranslatorarray = array();
		if (empty($handyatomtranslatorarray)) {
			// http://www.geocities.com/xhelmboyx/quicktime/formats/qtm-layout.txt
			// http://www.geocities.com/xhelmboyx/quicktime/formats/mp4-layout.txt
			// http://atomicparsley.sourceforge.net/mpeg-4files.html
			// https://code.google.com/p/mp4v2/wiki/iTunesMetadata
			$handyatomtranslatorarray["\xA9".'alb'] = 'album';               // iTunes 4.0
			$handyatomtranslatorarray["\xA9".'ART'] = 'artist';
			$handyatomtranslatorarray["\xA9".'art'] = 'artist';              // iTunes 4.0
			$handyatomtranslatorarray["\xA9".'aut'] = 'author';
			$handyatomtranslatorarray["\xA9".'cmt'] = 'comment';             // iTunes 4.0
			$handyatomtranslatorarray["\xA9".'com'] = 'comment';
			$handyatomtranslatorarray["\xA9".'cpy'] = 'copyright';
			$handyatomtranslatorarray["\xA9".'day'] = 'creation_date';       // iTunes 4.0
			$handyatomtranslatorarray["\xA9".'dir'] = 'director';
			$handyatomtranslatorarray["\xA9".'ed1'] = 'edit1';
			$handyatomtranslatorarray["\xA9".'ed2'] = 'edit2';
			$handyatomtranslatorarray["\xA9".'ed3'] = 'edit3';
			$handyatomtranslatorarray["\xA9".'ed4'] = 'edit4';
			$handyatomtranslatorarray["\xA9".'ed5'] = 'edit5';
			$handyatomtranslatorarray["\xA9".'ed6'] = 'edit6';
			$handyatomtranslatorarray["\xA9".'ed7'] = 'edit7';
			$handyatomtranslatorarray["\xA9".'ed8'] = 'edit8';
			$handyatomtranslatorarray["\xA9".'ed9'] = 'edit9';
			$handyatomtranslatorarray["\xA9".'enc'] = 'encoded_by';
			$handyatomtranslatorarray["\xA9".'fmt'] = 'format';
			$handyatomtranslatorarray["\xA9".'gen'] = 'genre';               // iTunes 4.0
			$handyatomtranslatorarray["\xA9".'grp'] = 'grouping';            // iTunes 4.2
			$handyatomtranslatorarray["\xA9".'hst'] = 'host_computer';
			$handyatomtranslatorarray["\xA9".'inf'] = 'information';
			$handyatomtranslatorarray["\xA9".'lyr'] = 'lyrics';              // iTunes 5.0
			$handyatomtranslatorarray["\xA9".'mak'] = 'make';
			$handyatomtranslatorarray["\xA9".'mod'] = 'model';
			$handyatomtranslatorarray["\xA9".'nam'] = 'title';               // iTunes 4.0
			$handyatomtranslatorarray["\xA9".'ope'] = 'composer';
			$handyatomtranslatorarray["\xA9".'prd'] = 'producer';
			$handyatomtranslatorarray["\xA9".'PRD'] = 'product';
			$handyatomtranslatorarray["\xA9".'prf'] = 'performers';
			$handyatomtranslatorarray["\xA9".'req'] = 'system_requirements';
			$handyatomtranslatorarray["\xA9".'src'] = 'source_credit';
			$handyatomtranslatorarray["\xA9".'swr'] = 'software';
			$handyatomtranslatorarray["\xA9".'too'] = 'encoding_tool';       // iTunes 4.0
			$handyatomtranslatorarray["\xA9".'trk'] = 'track_number';
			$handyatomtranslatorarray["\xA9".'url'] = 'url';
			$handyatomtranslatorarray["\xA9".'wrn'] = 'warning';
			$handyatomtranslatorarray["\xA9".'wrt'] = 'composer';
			$handyatomtranslatorarray['aART'] = 'album_artist';
			$handyatomtranslatorarray['apID'] = 'purchase_account';
			$handyatomtranslatorarray['catg'] = 'category';            // iTunes 4.9
			$handyatomtranslatorarray['covr'] = 'picture';             // iTunes 4.0
			$handyatomtranslatorarray['cpil'] = 'compilation';         // iTunes 4.0
			$handyatomtranslatorarray['cprt'] = 'copyright';           // iTunes 4.0?
			$handyatomtranslatorarray['desc'] = 'description';         // iTunes 5.0
			$handyatomtranslatorarray['disk'] = 'disc_number';         // iTunes 4.0
			$handyatomtranslatorarray['egid'] = 'episode_guid';        // iTunes 4.9
			$handyatomtranslatorarray['gnre'] = 'genre';               // iTunes 4.0
			$handyatomtranslatorarray['hdvd'] = 'hd_video';            // iTunes 4.0
			$handyatomtranslatorarray['ldes'] = 'description_long';    //
			$handyatomtranslatorarray['keyw'] = 'keyword';             // iTunes 4.9
			$handyatomtranslatorarray['pcst'] = 'podcast';             // iTunes 4.9
			$handyatomtranslatorarray['pgap'] = 'gapless_playback';    // iTunes 7.0
			$handyatomtranslatorarray['purd'] = 'purchase_date';       // iTunes 6.0.2
			$handyatomtranslatorarray['purl'] = 'podcast_url';         // iTunes 4.9
			$handyatomtranslatorarray['rtng'] = 'rating';              // iTunes 4.0
			$handyatomtranslatorarray['soaa'] = 'sort_album_artist';   //
			$handyatomtranslatorarray['soal'] = 'sort_album';          //
			$handyatomtranslatorarray['soar'] = 'sort_artist';         //
			$handyatomtranslatorarray['soco'] = 'sort_composer';       //
			$handyatomtranslatorarray['sonm'] = 'sort_title';          //
			$handyatomtranslatorarray['sosn'] = 'sort_show';           //
			$handyatomtranslatorarray['stik'] = 'stik';                // iTunes 4.9
			$handyatomtranslatorarray['tmpo'] = 'bpm';                 // iTunes 4.0
			$handyatomtranslatorarray['trkn'] = 'track_number';        // iTunes 4.0
			$handyatomtranslatorarray['tven'] = 'tv_episode_id';       //
			$handyatomtranslatorarray['tves'] = 'tv_episode';          // iTunes 6.0
			$handyatomtranslatorarray['tvnn'] = 'tv_network_name';     // iTunes 6.0
			$handyatomtranslatorarray['tvsh'] = 'tv_show_name';        // iTunes 6.0
			$handyatomtranslatorarray['tvsn'] = 'tv_season';           // iTunes 6.0

			// boxnames:
			/*
			$handyatomtranslatorarray['iTunSMPB']                    = 'iTunSMPB';
			$handyatomtranslatorarray['iTunNORM']                    = 'iTunNORM';
			$handyatomtranslatorarray['Encoding Params']             = 'Encoding Params';
			$handyatomtranslatorarray['replaygain_track_gain']       = 'replaygain_track_gain';
			$handyatomtranslatorarray['replaygain_track_peak']       = 'replaygain_track_peak';
			$handyatomtranslatorarray['replaygain_track_minmax']     = 'replaygain_track_minmax';
			$handyatomtranslatorarray['MusicIP PUID']                = 'MusicIP PUID';
			$handyatomtranslatorarray['MusicBrainz Artist Id']       = 'MusicBrainz Artist Id';
			$handyatomtranslatorarray['MusicBrainz Album Id']        = 'MusicBrainz Album Id';
			$handyatomtranslatorarray['MusicBrainz Album Artist Id'] = 'MusicBrainz Album Artist Id';
			$handyatomtranslatorarray['MusicBrainz Track Id']        = 'MusicBrainz Track Id';
			$handyatomtranslatorarray['MusicBrainz Disc Id']         = 'MusicBrainz Disc Id';

			// http://age.hobba.nl/audio/tag_frame_reference.html
			$handyatomtranslatorarray['PLAY_COUNTER']                = 'play_counter'; // Foobar2000 - https://www.getid3.org/phpBB3/viewtopic.php?t=1355
			$handyatomtranslatorarray['MEDIATYPE']                   = 'mediatype';    // Foobar2000 - https://www.getid3.org/phpBB3/viewtopic.php?t=1355
			*/
		}
		$info = &$this->getid3->info;
		$comment_key = '';
		if ($boxname && ($boxname != $keyname)) {
			$comment_key = (isset($handyatomtranslatorarray[$boxname]) ? $handyatomtranslatorarray[$boxname] : $boxname);
		} elseif (isset($handyatomtranslatorarray[$keyname])) {
			$comment_key = $handyatomtranslatorarray[$keyname];
		}
		if ($comment_key) {
			if ($comment_key == 'picture') {
				// already copied directly into [comments][picture] elsewhere, do not re-copy here
				return true;
			}
			$gooddata = array($data);
			if ($comment_key == 'genre') {
				// some other taggers separate multiple genres with semicolon, e.g. "Heavy Metal;Thrash Metal;Metal"
				$gooddata = explode(';', $data);
			}
			foreach ($gooddata as $data) {
				if (!empty($info['quicktime']['comments'][$comment_key]) && in_array($data, $info['quicktime']['comments'][$comment_key], true)) {
					// avoid duplicate copies of identical data
					continue;
				}
				$info['quicktime']['comments'][$comment_key][] = $data;
			}
		}
		return true;
	}

	/**
	 * @param string $lstring
	 * @param int    $count
	 *
	 * @return string
	 */
	public function LociString($lstring, &$count) {
		// Loci strings are UTF-8 or UTF-16 and null (x00/x0000) terminated. UTF-16 has a BOM
		// Also need to return the number of bytes the string occupied so additional fields can be extracted
		$len = strlen($lstring);
		if ($len == 0) {
			$count = 0;
			return '';
		}
		if ($lstring[0] == "\x00") {
			$count = 1;
			return '';
		}
		// check for BOM
		if (($len > 2) && ((($lstring[0] == "\xFE") && ($lstring[1] == "\xFF")) || (($lstring[0] == "\xFF") && ($lstring[1] == "\xFE")))) {
			// UTF-16
			if (preg_match('/(.*)\x00/', $lstring, $lmatches)) {
				$count = strlen($lmatches[1]) * 2 + 2; //account for 2 byte characters and trailing \x0000
				return getid3_lib::iconv_fallback_utf16_utf8($lmatches[1]);
			} else {
				return '';
			}
		}
		// UTF-8
		if (preg_match('/(.*)\x00/', $lstring, $lmatches)) {
			$count = strlen($lmatches[1]) + 1; //account for trailing \x00
			return $lmatches[1];
		}
		return '';
	}

	/**
	 * @param string $nullterminatedstring
	 *
	 * @return string
	 */
	public function NoNullString($nullterminatedstring) {
		// remove the single null terminator on null terminated strings
		if (substr($nullterminatedstring, strlen($nullterminatedstring) - 1, 1) === "\x00") {
			return substr($nullterminatedstring, 0, strlen($nullterminatedstring) - 1);
		}
		return $nullterminatedstring;
	}

	/**
	 * @param string $pascalstring
	 *
	 * @return string
	 */
	public function Pascal2String($pascalstring) {
		// Pascal strings have 1 unsigned byte at the beginning saying how many chars (1-255) are in the string
		return substr($pascalstring, 1);
	}

	/**
	 * @param string $pascalstring
	 *
	 * @return string
	 */
	public function MaybePascal2String($pascalstring) {
		// Pascal strings have 1 unsigned byte at the beginning saying how many chars (1-255) are in the string
		// Check if string actually is in this format or written incorrectly, straight string, or null-terminated string
		if (ord(substr($pascalstring, 0, 1)) == (strlen($pascalstring) - 1)) {
			return substr($pascalstring, 1);
		} elseif (substr($pascalstring, -1, 1) == "\x00") {
			// appears to be null-terminated instead of Pascal-style
			return substr($pascalstring, 0, -1);
		}
		return $pascalstring;
	}


	/**
	 * Helper functions for m4b audiobook chapters
	 * code by Steffen Hartmann 2015-Nov-08.
	 *
	 * @param array  $info
	 * @param string $tag
	 * @param string $history
	 * @param array  $result
	 */
	public function search_tag_by_key($info, $tag, $history, &$result) {
		foreach ($info as $key => $value) {
			$key_history = $history.'/'.$key;
			if ($key === $tag) {
				$result[] = array($key_history, $info);
			} else {
				if (is_array($value)) {
					$this->search_tag_by_key($value, $tag, $key_history, $result);
				}
			}
		}
	}

	/**
	 * @param array  $info
	 * @param string $k
	 * @param string $v
	 * @param string $history
	 * @param array  $result
	 */
	public function search_tag_by_pair($info, $k, $v, $history, &$result) {
		foreach ($info as $key => $value) {
			$key_history = $history.'/'.$key;
			if (($key === $k) && ($value === $v)) {
				$result[] = array($key_history, $info);
			} else {
				if (is_array($value)) {
					$this->search_tag_by_pair($value, $k, $v, $key_history, $result);
				}
			}
		}
	}

	/**
	 * @param array $info
	 *
	 * @return array
	 */
	public function quicktime_time_to_sample_table($info) {
		$res = array();
		$this->search_tag_by_pair($info['quicktime']['moov'], 'name', 'stbl', 'quicktime/moov', $res);
		foreach ($res as $value) {
			$stbl_res = array();
			$this->search_tag_by_pair($value[1], 'data_format', 'text', $value[0], $stbl_res);
			if (count($stbl_res) > 0) {
				$stts_res = array();
				$this->search_tag_by_key($value[1], 'time_to_sample_table', $value[0], $stts_res);
				if (count($stts_res) > 0) {
					return $stts_res[0][1]['time_to_sample_table'];
				}
			}
		}
		return array();
	}

	/**
	 * @param array $info
	 *
	 * @return int
	 */
	public function quicktime_bookmark_time_scale($info) {
		$time_scale = '';
		$ts_prefix_len = 0;
		$res = array();
		$this->search_tag_by_pair($info['quicktime']['moov'], 'name', 'stbl', 'quicktime/moov', $res);
		foreach ($res as $value) {
			$stbl_res = array();
			$this->search_tag_by_pair($value[1], 'data_format', 'text', $value[0], $stbl_res);
			if (count($stbl_res) > 0) {
				$ts_res = array();
				$this->search_tag_by_key($info['quicktime']['moov'], 'time_scale', 'quicktime/moov', $ts_res);
				foreach ($ts_res as $sub_value) {
					$prefix = substr($sub_value[0], 0, -12);
					if ((substr($stbl_res[0][0], 0, strlen($prefix)) === $prefix) && ($ts_prefix_len < strlen($prefix))) {
						$time_scale = $sub_value[1]['time_scale'];
						$ts_prefix_len = strlen($prefix);
					}
				}
			}
		}
		return $time_scale;
	}
	/*
	// END helper functions for m4b audiobook chapters
	*/


}
