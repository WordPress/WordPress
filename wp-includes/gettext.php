<?php
/*
   Copyright (c) 2003 Danilo Segan <danilo@kvota.net>.

   This file is part of PHP-gettext.

   PHP-gettext is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   PHP-gettext is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with PHP-gettext; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
 


  // For start, we only want to read the MO files

class gettext_reader {
//public:
  var $error = 0; // public variable that holds error code (0 if no error)
//private:
  var $BYTEORDER = 0;
  var $STREAM = NULL;
  var $short_circuit = false;

  function readint() {
    // Reads 4 byte value from $FD and puts it in int
    // $BYTEORDER specifies the byte order: 0 low endian, 1 big endian
    for ($i=0; $i<4; $i++) {
      $byte[$i]=ord($this->STREAM->read(1));
    }
    //print sprintf("pos: %d\n",$this->STREAM->currentpos());
    if ($this->BYTEORDER == 0) 
      return (int)(($byte[0]) | ($byte[1]<<8) | ($byte[2]<<16) | ($byte[3]<<24));
    else 
      return (int)(($byte[3]) | ($byte[2]<<8) | ($byte[1]<<16) | ($byte[0]<<24));
  }

  // constructor that requires StreamReader object
  function gettext_reader($Reader) {
    // If there isn't a StreamReader, turn on short circuit mode.
    if (! $Reader) {
        $this->short_circuit = true;
        return;
    }

	// $MAGIC1 = (int)0x950412de; //bug in PHP 5
	$MAGIC1 = (int) - 1794895138;
	// $MAGIC2 = (int)0xde120495; //bug
	$MAGIC2 = (int) - 569244523;


    $this->STREAM = $Reader;
    $magic = $this->readint();
    if ($magic == $MAGIC1) {
      $this->BYTEORDER = 0;
    } elseif ($magic == $MAGIC2) {
      $this->BYTEORDER = 1;
    } else {
      $this->error = 1; // not MO file
      return false;
    }

    // FIXME: Do we care about revision? We should.
    $revision = $this->readint();

    $total = $this->readint();
    $originals = $this->readint();
    $translations = $this->readint();

    $this->total = $total;
    $this->originals = $originals;
    $this->translations = $translations;

  }

  function load_tables($translations=false) {
    // if tables are loaded do not load them again
    if (!is_array($this->ORIGINALS)) {
      $this->ORIGINALS = array();
      $this->STREAM->seekto($this->originals);
      for ($i=0; $i<$this->total; $i++) {
	$len = $this->readint();
	$ofs = $this->readint();
	$this->ORIGINALS[] = array($len,$ofs);
      }
    }

    // similar for translations
    if ($translations and !is_array($this->TRANSLATIONS)) {
      $this->TRANSLATIONS = array();
      $this->STREAM->seekto($this->translations);
      for ($i=0; $i<$this->total; $i++) {
	$len = $this->readint();
	$ofs = $this->readint();
	$this->TRANSLATIONS[] = array($len,$ofs);
      }
    }

  }

  function get_string_number($num) {
    // get a string with particular number
    // TODO: Add simple hashing [check array, add if not already there]
    $this->load_tables();
    $meta = $this->ORIGINALS[$num];
    $length = $meta[0];
    $offset = $meta[1];
		if (! $length) {
			return '';
		}
    $this->STREAM->seekto($offset);
    $data = $this->STREAM->read($length);
    return (string)$data;
  }

  function get_translation_number($num) {
    // get a string with particular number
    // TODO: Add simple hashing [check array, add if not already there]
    $this->load_tables(true);
    $meta = $this->TRANSLATIONS[$num];
    $length = $meta[0];
    $offset = $meta[1];
    $this->STREAM->seekto($offset);
    $data = $this->STREAM->read($length);
    return (string)$data;
  }
  
  // binary search for string
  function find_string($string, $start,$end) {
    //print "start: $start, end: $end\n";
    if (abs($start-$end)<=1) {
      // we're done, if it's not it, bye bye
      $txt = $this->get_string_number($start);
      if ($string == $txt)
	return $start;
      else
	return -1;
    } elseif ($start>$end) {
      return $this->find_string($string,$end,$start);
    }  else {
      $half = (int)(($start+$end)/2);
      $tst = $this->get_string_number($half);
      $cmp = strcmp($string,$tst);
      if ($cmp == 0) 
	return $half;
      elseif ($cmp<0) 
	return $this->find_string($string,$start,$half);
      else
	return $this->find_string($string,$half,$end);
    }
  }

  function translate($string) {
    if ($this->short_circuit) {
        return $string;
    }

    $num = $this->find_string($string, 0, $this->total);
    if ($num == -1)
      return $string;
    else 
      return $this->get_translation_number($num);
  }

  function get_plural_forms() {
    // lets assume message number 0 is header
    // this is true, right?

    // cache header field for plural forms
    if (is_string($this->pluralheader)) 
      return $this->pluralheader;
    else {
      $header = $this->get_translation_number(0);

      if (eregi("plural-forms: (.*)\n",$header,$regs)) {
	$expr = $regs[1];
      } else {
	$expr = "nplurals=2; plural=n == 1 ? 0 : 1;";
      }
      $this->pluralheader = $expr;
      return $expr;
    }
  }

  function select_string($n) {
    $string = $this->get_plural_forms();
    $string = str_replace('nplurals',"\$total",$string);
    $string = str_replace("n",$n,$string);
    $string = str_replace('plural',"\$plural",$string);

    $total = 0;
    $plural = 0;

    eval("$string");
    if ($plural>=$total) $plural = 0;
    return $plural;
  }

  function ngettext($single, $plural, $number) {
    if ($this->short_circuit) {
      if ($number != 1) return $plural;
      else return $single;
    }

    // find out the appropriate form
    $select = $this->select_string($number); 
    

    // this should contains all strings separated by NULLs
    $result = $this->find_string($single.chr(0).$plural,0,$this->total);
    if ($result == -1) {
      if ($number != 1) return $plural;
      else return $single;
    } else {
      $result = $this->get_translation_number($result);
    
      // lets try to parse all the NUL staff
      //$result = "proba0".chr(0)."proba1".chr(0)."proba2";
      $list = explode (chr(0), $result);
      return $list[$select];
    }
  }

}


?>