PHP_ARG_WITH(maxminddb,
    [Whether to enable the MaxMind DB Reader extension],
    [  --with-maxminddb      Enable MaxMind DB Reader extension support])

PHP_ARG_ENABLE(maxminddb-debug, for MaxMind DB debug support,
    [ --enable-maxminddb-debug    Enable enable MaxMind DB deubg support], no, no)

if test $PHP_MAXMINDDB != "no"; then

    AC_PATH_PROG(PKG_CONFIG, pkg-config, no)

    AC_MSG_CHECKING(for libmaxminddb)
    if test -x "$PKG_CONFIG" && $PKG_CONFIG --exists libmaxminddb; then
        dnl retrieve build options from pkg-config
        if $PKG_CONFIG libmaxminddb --atleast-version 1.0.0; then
            LIBMAXMINDDB_INC=`$PKG_CONFIG libmaxminddb --cflags`
            LIBMAXMINDDB_LIB=`$PKG_CONFIG libmaxminddb --libs`
            LIBMAXMINDDB_VER=`$PKG_CONFIG libmaxminddb --modversion`
            AC_MSG_RESULT(found version $LIBMAXMINDDB_VER)
        else
            AC_MSG_ERROR(system libmaxminddb must be upgraded to version >= 1.0.0)
        fi
        PHP_EVAL_LIBLINE($LIBMAXMINDDB_LIB, MAXMINDDB_SHARED_LIBADD)
        PHP_EVAL_INCLINE($LIBMAXMINDDB_INC)
    else
        AC_MSG_RESULT(pkg-config information missing)
        AC_MSG_WARN(will use libmaxmxinddb from compiler default path)

        PHP_CHECK_LIBRARY(maxminddb, MMDB_open)
        PHP_ADD_LIBRARY(maxminddb, 1, MAXMINDDB_SHARED_LIBADD)
    fi

    if test $PHP_MAXMINDDB_DEBUG != "no"; then
        CFLAGS="$CFLAGS -Wall -Wextra -Wno-unused-parameter -Wno-missing-field-initializers -Werror"
    fi

    PHP_SUBST(MAXMINDDB_SHARED_LIBADD)

    PHP_NEW_EXTENSION(maxminddb, maxminddb.c, $ext_shared)
fi
