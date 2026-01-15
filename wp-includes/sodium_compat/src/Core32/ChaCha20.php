<?php

if (class_exists('ParagonIE_Sodium_Core32_ChaCha20', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core32_ChaCha20
 */
class ParagonIE_Sodium_Core32_ChaCha20 extends ParagonIE_Sodium_Core32_Util
{
    /**
     * The ChaCha20 quarter round function. Works on four 32-bit integers.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Int32 $a
     * @param ParagonIE_Sodium_Core32_Int32 $b
     * @param ParagonIE_Sodium_Core32_Int32 $c
     * @param ParagonIE_Sodium_Core32_Int32 $d
     * @return array<int, ParagonIE_Sodium_Core32_Int32>
     * @throws SodiumException
     * @throws TypeError
     */
    protected static function quarterRound(
        ParagonIE_Sodium_Core32_Int32 $a,
        ParagonIE_Sodium_Core32_Int32 $b,
        ParagonIE_Sodium_Core32_Int32 $c,
        ParagonIE_Sodium_Core32_Int32 $d
    ) {
        /** @var ParagonIE_Sodium_Core32_Int32 $a */
        /** @var ParagonIE_Sodium_Core32_Int32 $b */
        /** @var ParagonIE_Sodium_Core32_Int32 $c */
        /** @var ParagonIE_Sodium_Core32_Int32 $d */

        # a = PLUS(a,b); d = ROTATE(XOR(d,a),16);
        $a = $a->addInt32($b);
        $d = $d->xorInt32($a)->rotateLeft(16);

        # c = PLUS(c,d); b = ROTATE(XOR(b,c),12);
        $c = $c->addInt32($d);
        $b = $b->xorInt32($c)->rotateLeft(12);

        # a = PLUS(a,b); d = ROTATE(XOR(d,a), 8);
        $a = $a->addInt32($b);
        $d = $d->xorInt32($a)->rotateLeft(8);

        # c = PLUS(c,d); b = ROTATE(XOR(b,c), 7);
        $c = $c->addInt32($d);
        $b = $b->xorInt32($c)->rotateLeft(7);

        return array($a, $b, $c, $d);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_ChaCha20_Ctx $ctx
     * @param string $message
     *
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function encryptBytes(
        ParagonIE_Sodium_Core32_ChaCha20_Ctx $ctx,
        $message = ''
    ) {
        $bytes = self::strlen($message);

        /** @var ParagonIE_Sodium_Core32_Int32 $x0 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x1 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x2 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x3 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x4 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x5 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x6 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x7 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x8 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x9 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x10 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x11 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x12 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x13 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x14 */
        /** @var ParagonIE_Sodium_Core32_Int32 $x15 */

        /*
        j0 = ctx->input[0];
        j1 = ctx->input[1];
        j2 = ctx->input[2];
        j3 = ctx->input[3];
        j4 = ctx->input[4];
        j5 = ctx->input[5];
        j6 = ctx->input[6];
        j7 = ctx->input[7];
        j8 = ctx->input[8];
        j9 = ctx->input[9];
        j10 = ctx->input[10];
        j11 = ctx->input[11];
        j12 = ctx->input[12];
        j13 = ctx->input[13];
        j14 = ctx->input[14];
        j15 = ctx->input[15];
        */
        /** @var ParagonIE_Sodium_Core32_Int32 $j0 */
        $j0  = $ctx[0];
        /** @var ParagonIE_Sodium_Core32_Int32 $j1 */
        $j1  = $ctx[1];
        /** @var ParagonIE_Sodium_Core32_Int32 $j2 */
        $j2  = $ctx[2];
        /** @var ParagonIE_Sodium_Core32_Int32 $j3 */
        $j3  = $ctx[3];
        /** @var ParagonIE_Sodium_Core32_Int32 $j4 */
        $j4  = $ctx[4];
        /** @var ParagonIE_Sodium_Core32_Int32 $j5 */
        $j5  = $ctx[5];
        /** @var ParagonIE_Sodium_Core32_Int32 $j6 */
        $j6  = $ctx[6];
        /** @var ParagonIE_Sodium_Core32_Int32 $j7 */
        $j7  = $ctx[7];
        /** @var ParagonIE_Sodium_Core32_Int32 $j8 */
        $j8  = $ctx[8];
        /** @var ParagonIE_Sodium_Core32_Int32 $j9 */
        $j9  = $ctx[9];
        /** @var ParagonIE_Sodium_Core32_Int32 $j10 */
        $j10 = $ctx[10];
        /** @var ParagonIE_Sodium_Core32_Int32 $j11 */
        $j11 = $ctx[11];
        /** @var ParagonIE_Sodium_Core32_Int32 $j12 */
        $j12 = $ctx[12];
        /** @var ParagonIE_Sodium_Core32_Int32 $j13 */
        $j13 = $ctx[13];
        /** @var ParagonIE_Sodium_Core32_Int32 $j14 */
        $j14 = $ctx[14];
        /** @var ParagonIE_Sodium_Core32_Int32 $j15 */
        $j15 = $ctx[15];

        $c = '';
        for (;;) {
            if ($bytes < 64) {
                $message .= str_repeat("\x00", 64 - $bytes);
            }

            $x0 =  clone $j0;
            $x1 =  clone $j1;
            $x2 =  clone $j2;
            $x3 =  clone $j3;
            $x4 =  clone $j4;
            $x5 =  clone $j5;
            $x6 =  clone $j6;
            $x7 =  clone $j7;
            $x8 =  clone $j8;
            $x9 =  clone $j9;
            $x10 = clone $j10;
            $x11 = clone $j11;
            $x12 = clone $j12;
            $x13 = clone $j13;
            $x14 = clone $j14;
            $x15 = clone $j15;

            # for (i = 20; i > 0; i -= 2) {
            for ($i = 20; $i > 0; $i -= 2) {
                # QUARTERROUND( x0,  x4,  x8,  x12)
                list($x0, $x4, $x8, $x12) = self::quarterRound($x0, $x4, $x8, $x12);

                # QUARTERROUND( x1,  x5,  x9,  x13)
                list($x1, $x5, $x9, $x13) = self::quarterRound($x1, $x5, $x9, $x13);

                # QUARTERROUND( x2,  x6,  x10,  x14)
                list($x2, $x6, $x10, $x14) = self::quarterRound($x2, $x6, $x10, $x14);

                # QUARTERROUND( x3,  x7,  x11,  x15)
                list($x3, $x7, $x11, $x15) = self::quarterRound($x3, $x7, $x11, $x15);

                # QUARTERROUND( x0,  x5,  x10,  x15)
                list($x0, $x5, $x10, $x15) = self::quarterRound($x0, $x5, $x10, $x15);

                # QUARTERROUND( x1,  x6,  x11,  x12)
                list($x1, $x6, $x11, $x12) = self::quarterRound($x1, $x6, $x11, $x12);

                # QUARTERROUND( x2,  x7,  x8,  x13)
                list($x2, $x7, $x8, $x13) = self::quarterRound($x2, $x7, $x8, $x13);

                # QUARTERROUND( x3,  x4,  x9,  x14)
                list($x3, $x4, $x9, $x14) = self::quarterRound($x3, $x4, $x9, $x14);
            }
            /*
            x0 = PLUS(x0, j0);
            x1 = PLUS(x1, j1);
            x2 = PLUS(x2, j2);
            x3 = PLUS(x3, j3);
            x4 = PLUS(x4, j4);
            x5 = PLUS(x5, j5);
            x6 = PLUS(x6, j6);
            x7 = PLUS(x7, j7);
            x8 = PLUS(x8, j8);
            x9 = PLUS(x9, j9);
            x10 = PLUS(x10, j10);
            x11 = PLUS(x11, j11);
            x12 = PLUS(x12, j12);
            x13 = PLUS(x13, j13);
            x14 = PLUS(x14, j14);
            x15 = PLUS(x15, j15);
            */
            $x0 = $x0->addInt32($j0);
            $x1 = $x1->addInt32($j1);
            $x2 = $x2->addInt32($j2);
            $x3 = $x3->addInt32($j3);
            $x4 = $x4->addInt32($j4);
            $x5 = $x5->addInt32($j5);
            $x6 = $x6->addInt32($j6);
            $x7 = $x7->addInt32($j7);
            $x8 = $x8->addInt32($j8);
            $x9 = $x9->addInt32($j9);
            $x10 = $x10->addInt32($j10);
            $x11 = $x11->addInt32($j11);
            $x12 = $x12->addInt32($j12);
            $x13 = $x13->addInt32($j13);
            $x14 = $x14->addInt32($j14);
            $x15 = $x15->addInt32($j15);

            /*
            x0 = XOR(x0, LOAD32_LE(m + 0));
            x1 = XOR(x1, LOAD32_LE(m + 4));
            x2 = XOR(x2, LOAD32_LE(m + 8));
            x3 = XOR(x3, LOAD32_LE(m + 12));
            x4 = XOR(x4, LOAD32_LE(m + 16));
            x5 = XOR(x5, LOAD32_LE(m + 20));
            x6 = XOR(x6, LOAD32_LE(m + 24));
            x7 = XOR(x7, LOAD32_LE(m + 28));
            x8 = XOR(x8, LOAD32_LE(m + 32));
            x9 = XOR(x9, LOAD32_LE(m + 36));
            x10 = XOR(x10, LOAD32_LE(m + 40));
            x11 = XOR(x11, LOAD32_LE(m + 44));
            x12 = XOR(x12, LOAD32_LE(m + 48));
            x13 = XOR(x13, LOAD32_LE(m + 52));
            x14 = XOR(x14, LOAD32_LE(m + 56));
            x15 = XOR(x15, LOAD32_LE(m + 60));
            */
            $x0  =  $x0->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message,  0, 4)));
            $x1  =  $x1->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message,  4, 4)));
            $x2  =  $x2->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message,  8, 4)));
            $x3  =  $x3->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 12, 4)));
            $x4  =  $x4->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 16, 4)));
            $x5  =  $x5->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 20, 4)));
            $x6  =  $x6->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 24, 4)));
            $x7  =  $x7->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 28, 4)));
            $x8  =  $x8->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 32, 4)));
            $x9  =  $x9->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 36, 4)));
            $x10 = $x10->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 40, 4)));
            $x11 = $x11->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 44, 4)));
            $x12 = $x12->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 48, 4)));
            $x13 = $x13->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 52, 4)));
            $x14 = $x14->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 56, 4)));
            $x15 = $x15->xorInt32(ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 60, 4)));

            /*
                j12 = PLUSONE(j12);
                if (!j12) {
                    j13 = PLUSONE(j13);
                }
             */
            /** @var ParagonIE_Sodium_Core32_Int32 $j12 */
            $j12 = $j12->addInt(1);
            if ($j12->limbs[0] === 0 && $j12->limbs[1] === 0) {
                $j13 = $j13->addInt(1);
            }

            /*
            STORE32_LE(c + 0, x0);
            STORE32_LE(c + 4, x1);
            STORE32_LE(c + 8, x2);
            STORE32_LE(c + 12, x3);
            STORE32_LE(c + 16, x4);
            STORE32_LE(c + 20, x5);
            STORE32_LE(c + 24, x6);
            STORE32_LE(c + 28, x7);
            STORE32_LE(c + 32, x8);
            STORE32_LE(c + 36, x9);
            STORE32_LE(c + 40, x10);
            STORE32_LE(c + 44, x11);
            STORE32_LE(c + 48, x12);
            STORE32_LE(c + 52, x13);
            STORE32_LE(c + 56, x14);
            STORE32_LE(c + 60, x15);
            */

            $block = $x0->toReverseString() .
                $x1->toReverseString() .
                $x2->toReverseString() .
                $x3->toReverseString() .
                $x4->toReverseString() .
                $x5->toReverseString() .
                $x6->toReverseString() .
                $x7->toReverseString() .
                $x8->toReverseString() .
                $x9->toReverseString() .
                $x10->toReverseString() .
                $x11->toReverseString() .
                $x12->toReverseString() .
                $x13->toReverseString() .
                $x14->toReverseString() .
                $x15->toReverseString();

            /* Partial block */
            if ($bytes < 64) {
                $c .= self::substr($block, 0, $bytes);
                break;
            }

            /* Full block */
            $c .= $block;
            $bytes -= 64;
            if ($bytes <= 0) {
                break;
            }
            $message = self::substr($message, 64);
        }
        /* end for(;;) loop */

        $ctx[12] = $j12;
        $ctx[13] = $j13;
        return $c;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $len
     * @param string $nonce
     * @param string $key
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function stream($len = 64, $nonce = '', $key = '')
    {
        return self::encryptBytes(
            new ParagonIE_Sodium_Core32_ChaCha20_Ctx($key, $nonce),
            str_repeat("\x00", $len)
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $len
     * @param string $nonce
     * @param string $key
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ietfStream($len, $nonce = '', $key = '')
    {
        return self::encryptBytes(
            new ParagonIE_Sodium_Core32_ChaCha20_IetfCtx($key, $nonce),
            str_repeat("\x00", $len)
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $message
     * @param string $nonce
     * @param string $key
     * @param string $ic
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ietfStreamXorIc($message, $nonce = '', $key = '', $ic = '')
    {
        return self::encryptBytes(
            new ParagonIE_Sodium_Core32_ChaCha20_IetfCtx($key, $nonce, $ic),
            $message
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $message
     * @param string $nonce
     * @param string $key
     * @param string $ic
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function streamXorIc($message, $nonce = '', $key = '', $ic = '')
    {
        return self::encryptBytes(
            new ParagonIE_Sodium_Core32_ChaCha20_Ctx($key, $nonce, $ic),
            $message
        );
    }
}
