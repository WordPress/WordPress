/* MaxMind, Inc., licenses this file to you under the Apache License, Version
 * 2.0 (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

#include "php_maxminddb.h"

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include <php.h>
#include <zend.h>

#include "Zend/zend_exceptions.h"
#include "Zend/zend_types.h"
#include "ext/spl/spl_exceptions.h"
#include "ext/standard/info.h"
#include <maxminddb.h>

#ifdef ZTS
#include <TSRM.h>
#endif

#define __STDC_FORMAT_MACROS
#include <inttypes.h>

#define PHP_MAXMINDDB_NS ZEND_NS_NAME("MaxMind", "Db")
#define PHP_MAXMINDDB_READER_NS ZEND_NS_NAME(PHP_MAXMINDDB_NS, "Reader")
#define PHP_MAXMINDDB_METADATA_NS                                              \
    ZEND_NS_NAME(PHP_MAXMINDDB_READER_NS, "Metadata")
#define PHP_MAXMINDDB_READER_EX_NS                                             \
    ZEND_NS_NAME(PHP_MAXMINDDB_READER_NS, "InvalidDatabaseException")

#define Z_MAXMINDDB_P(zv) php_maxminddb_fetch_object(Z_OBJ_P(zv))
typedef size_t strsize_t;
typedef zend_object free_obj_t;

/* For PHP 8 compatibility */
#if PHP_VERSION_ID < 80000

#define PROP_OBJ(zv) (zv)

#else

#define PROP_OBJ(zv) Z_OBJ_P(zv)

#define TSRMLS_C
#define TSRMLS_CC
#define TSRMLS_DC

/* End PHP 8 compatibility */
#endif

#ifndef ZEND_ACC_CTOR
#define ZEND_ACC_CTOR 0
#endif

/* IS_MIXED was added in 2020 */
#ifndef IS_MIXED
#define IS_MIXED IS_UNDEF
#endif

/* ZEND_THIS was added in 7.4 */
#ifndef ZEND_THIS
#define ZEND_THIS (&EX(This))
#endif

typedef struct _maxminddb_obj {
    MMDB_s *mmdb;
    zend_object std;
} maxminddb_obj;

PHP_FUNCTION(maxminddb);

static int
get_record(INTERNAL_FUNCTION_PARAMETERS, zval *record, int *prefix_len);
static const MMDB_entry_data_list_s *
handle_entry_data_list(const MMDB_entry_data_list_s *entry_data_list,
                       zval *z_value TSRMLS_DC);
static const MMDB_entry_data_list_s *
handle_array(const MMDB_entry_data_list_s *entry_data_list,
             zval *z_value TSRMLS_DC);
static const MMDB_entry_data_list_s *
handle_map(const MMDB_entry_data_list_s *entry_data_list,
           zval *z_value TSRMLS_DC);
static void handle_uint128(const MMDB_entry_data_list_s *entry_data_list,
                           zval *z_value TSRMLS_DC);
static void handle_uint64(const MMDB_entry_data_list_s *entry_data_list,
                          zval *z_value TSRMLS_DC);
static void handle_uint32(const MMDB_entry_data_list_s *entry_data_list,
                          zval *z_value TSRMLS_DC);

#define CHECK_ALLOCATED(val)                                                   \
    if (!val) {                                                                \
        zend_error(E_ERROR, "Out of memory");                                  \
        return;                                                                \
    }

static zend_object_handlers maxminddb_obj_handlers;
static zend_class_entry *maxminddb_ce, *maxminddb_exception_ce, *metadata_ce;

static inline maxminddb_obj *
php_maxminddb_fetch_object(zend_object *obj TSRMLS_DC) {
    return (maxminddb_obj *)((char *)(obj)-XtOffsetOf(maxminddb_obj, std));
}

ZEND_BEGIN_ARG_INFO_EX(arginfo_maxminddbreader_construct, 0, 0, 1)
ZEND_ARG_TYPE_INFO(0, db_file, IS_STRING, 0)
ZEND_END_ARG_INFO()

PHP_METHOD(MaxMind_Db_Reader, __construct) {
    char *db_file = NULL;
    strsize_t name_len;
    zval *_this_zval = NULL;

    if (zend_parse_method_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                                     getThis(),
                                     "Os",
                                     &_this_zval,
                                     maxminddb_ce,
                                     &db_file,
                                     &name_len) == FAILURE) {
        return;
    }

    if (0 != php_check_open_basedir(db_file TSRMLS_CC) ||
        0 != access(db_file, R_OK)) {
        zend_throw_exception_ex(
            spl_ce_InvalidArgumentException,
            0 TSRMLS_CC,
            "The file \"%s\" does not exist or is not readable.",
            db_file);
        return;
    }

    MMDB_s *mmdb = (MMDB_s *)ecalloc(1, sizeof(MMDB_s));
    uint16_t status = MMDB_open(db_file, MMDB_MODE_MMAP, mmdb);

    if (MMDB_SUCCESS != status) {
        zend_throw_exception_ex(
            maxminddb_exception_ce,
            0 TSRMLS_CC,
            "Error opening database file (%s). Is this a valid "
            "MaxMind DB file?",
            db_file);
        efree(mmdb);
        return;
    }

    maxminddb_obj *mmdb_obj = Z_MAXMINDDB_P(ZEND_THIS);
    mmdb_obj->mmdb = mmdb;
}

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(
    arginfo_maxminddbreader_get, 0, 1, IS_MIXED, 1)
ZEND_ARG_TYPE_INFO(0, ip_address, IS_STRING, 0)
ZEND_END_ARG_INFO()

PHP_METHOD(MaxMind_Db_Reader, get) {
    int prefix_len = 0;
    get_record(INTERNAL_FUNCTION_PARAM_PASSTHRU, return_value, &prefix_len);
}

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(
    arginfo_maxminddbreader_getWithPrefixLen, 0, 1, IS_ARRAY, 1)
ZEND_ARG_TYPE_INFO(0, ip_address, IS_STRING, 0)
ZEND_END_ARG_INFO()

PHP_METHOD(MaxMind_Db_Reader, getWithPrefixLen) {
    zval record, z_prefix_len;

    int prefix_len = 0;
    if (get_record(INTERNAL_FUNCTION_PARAM_PASSTHRU, &record, &prefix_len) ==
        FAILURE) {
        return;
    }

    array_init(return_value);
    add_next_index_zval(return_value, &record);

    ZVAL_LONG(&z_prefix_len, prefix_len);
    add_next_index_zval(return_value, &z_prefix_len);
}

static int
get_record(INTERNAL_FUNCTION_PARAMETERS, zval *record, int *prefix_len) {
    char *ip_address = NULL;
    strsize_t name_len;
    zval *this_zval = NULL;

    if (zend_parse_method_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                                     getThis(),
                                     "Os",
                                     &this_zval,
                                     maxminddb_ce,
                                     &ip_address,
                                     &name_len) == FAILURE) {
        return FAILURE;
    }

    const maxminddb_obj *mmdb_obj = (maxminddb_obj *)Z_MAXMINDDB_P(ZEND_THIS);

    MMDB_s *mmdb = mmdb_obj->mmdb;

    if (NULL == mmdb) {
        zend_throw_exception_ex(spl_ce_BadMethodCallException,
                                0 TSRMLS_CC,
                                "Attempt to read from a closed MaxMind DB.");
        return FAILURE;
    }

    struct addrinfo hints = {
        .ai_family = AF_UNSPEC,
        .ai_flags = AI_NUMERICHOST,
        /* We set ai_socktype so that we only get one result back */
        .ai_socktype = SOCK_STREAM};

    struct addrinfo *addresses = NULL;
    int gai_status = getaddrinfo(ip_address, NULL, &hints, &addresses);
    if (gai_status) {
        zend_throw_exception_ex(spl_ce_InvalidArgumentException,
                                0 TSRMLS_CC,
                                "The value \"%s\" is not a valid IP address.",
                                ip_address);
        return FAILURE;
    }
    if (!addresses || !addresses->ai_addr) {
        zend_throw_exception_ex(
            spl_ce_InvalidArgumentException,
            0 TSRMLS_CC,
            "getaddrinfo was successful but failed to set the addrinfo");
        return FAILURE;
    }

    int sa_family = addresses->ai_addr->sa_family;

    int mmdb_error = MMDB_SUCCESS;
    MMDB_lookup_result_s result =
        MMDB_lookup_sockaddr(mmdb, addresses->ai_addr, &mmdb_error);

    freeaddrinfo(addresses);

    if (MMDB_SUCCESS != mmdb_error) {
        zend_class_entry *ex;
        if (MMDB_IPV6_LOOKUP_IN_IPV4_DATABASE_ERROR == mmdb_error) {
            ex = spl_ce_InvalidArgumentException;
        } else {
            ex = maxminddb_exception_ce;
        }
        zend_throw_exception_ex(ex,
                                0 TSRMLS_CC,
                                "Error looking up %s. %s",
                                ip_address,
                                MMDB_strerror(mmdb_error));
        return FAILURE;
    }

    *prefix_len = result.netmask;

    if (sa_family == AF_INET && mmdb->metadata.ip_version == 6) {
        /* We return the prefix length given the IPv4 address. If there is
           no IPv4 subtree, we return a prefix length of 0. */
        *prefix_len = *prefix_len >= 96 ? *prefix_len - 96 : 0;
    }

    if (!result.found_entry) {
        ZVAL_NULL(record);
        return SUCCESS;
    }

    MMDB_entry_data_list_s *entry_data_list = NULL;
    int status = MMDB_get_entry_data_list(&result.entry, &entry_data_list);

    if (MMDB_SUCCESS != status) {
        zend_throw_exception_ex(maxminddb_exception_ce,
                                0 TSRMLS_CC,
                                "Error while looking up data for %s. %s",
                                ip_address,
                                MMDB_strerror(status));
        MMDB_free_entry_data_list(entry_data_list);
        return FAILURE;
    } else if (NULL == entry_data_list) {
        zend_throw_exception_ex(
            maxminddb_exception_ce,
            0 TSRMLS_CC,
            "Error while looking up data for %s. Your database may "
            "be corrupt or you have found a bug in libmaxminddb.",
            ip_address);
        return FAILURE;
    }

    const MMDB_entry_data_list_s *rv =
        handle_entry_data_list(entry_data_list, record TSRMLS_CC);
    if (rv == NULL) {
        /* We should have already thrown the exception in handle_entry_data_list
         */
        return FAILURE;
    }
    MMDB_free_entry_data_list(entry_data_list);
    return SUCCESS;
}

ZEND_BEGIN_ARG_INFO_EX(arginfo_maxminddbreader_void, 0, 0, 0)
ZEND_END_ARG_INFO()

PHP_METHOD(MaxMind_Db_Reader, metadata) {
    zval *this_zval = NULL;

    if (zend_parse_method_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                                     getThis(),
                                     "O",
                                     &this_zval,
                                     maxminddb_ce) == FAILURE) {
        return;
    }

    const maxminddb_obj *const mmdb_obj =
        (maxminddb_obj *)Z_MAXMINDDB_P(this_zval);

    if (NULL == mmdb_obj->mmdb) {
        zend_throw_exception_ex(spl_ce_BadMethodCallException,
                                0 TSRMLS_CC,
                                "Attempt to read from a closed MaxMind DB.");
        return;
    }

    object_init_ex(return_value, metadata_ce);

    MMDB_entry_data_list_s *entry_data_list;
    MMDB_get_metadata_as_entry_data_list(mmdb_obj->mmdb, &entry_data_list);

    zval metadata_array;
    const MMDB_entry_data_list_s *rv =
        handle_entry_data_list(entry_data_list, &metadata_array TSRMLS_CC);
    if (rv == NULL) {
        return;
    }
    MMDB_free_entry_data_list(entry_data_list);
    zend_call_method_with_1_params(PROP_OBJ(return_value),
                                   metadata_ce,
                                   &metadata_ce->constructor,
                                   ZEND_CONSTRUCTOR_FUNC_NAME,
                                   NULL,
                                   &metadata_array);
    zval_ptr_dtor(&metadata_array);
}

PHP_METHOD(MaxMind_Db_Reader, close) {
    zval *this_zval = NULL;

    if (zend_parse_method_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                                     getThis(),
                                     "O",
                                     &this_zval,
                                     maxminddb_ce) == FAILURE) {
        return;
    }

    maxminddb_obj *mmdb_obj = (maxminddb_obj *)Z_MAXMINDDB_P(this_zval);

    if (NULL == mmdb_obj->mmdb) {
        zend_throw_exception_ex(spl_ce_BadMethodCallException,
                                0 TSRMLS_CC,
                                "Attempt to close a closed MaxMind DB.");
        return;
    }
    MMDB_close(mmdb_obj->mmdb);
    efree(mmdb_obj->mmdb);
    mmdb_obj->mmdb = NULL;
}

static const MMDB_entry_data_list_s *
handle_entry_data_list(const MMDB_entry_data_list_s *entry_data_list,
                       zval *z_value TSRMLS_DC) {
    switch (entry_data_list->entry_data.type) {
        case MMDB_DATA_TYPE_MAP:
            return handle_map(entry_data_list, z_value TSRMLS_CC);
        case MMDB_DATA_TYPE_ARRAY:
            return handle_array(entry_data_list, z_value TSRMLS_CC);
        case MMDB_DATA_TYPE_UTF8_STRING:
            ZVAL_STRINGL(z_value,
                         (char *)entry_data_list->entry_data.utf8_string,
                         entry_data_list->entry_data.data_size);
            break;
        case MMDB_DATA_TYPE_BYTES:
            ZVAL_STRINGL(z_value,
                         (char *)entry_data_list->entry_data.bytes,
                         entry_data_list->entry_data.data_size);
            break;
        case MMDB_DATA_TYPE_DOUBLE:
            ZVAL_DOUBLE(z_value, entry_data_list->entry_data.double_value);
            break;
        case MMDB_DATA_TYPE_FLOAT:
            ZVAL_DOUBLE(z_value, entry_data_list->entry_data.float_value);
            break;
        case MMDB_DATA_TYPE_UINT16:
            ZVAL_LONG(z_value, entry_data_list->entry_data.uint16);
            break;
        case MMDB_DATA_TYPE_UINT32:
            handle_uint32(entry_data_list, z_value TSRMLS_CC);
            break;
        case MMDB_DATA_TYPE_BOOLEAN:
            ZVAL_BOOL(z_value, entry_data_list->entry_data.boolean);
            break;
        case MMDB_DATA_TYPE_UINT64:
            handle_uint64(entry_data_list, z_value TSRMLS_CC);
            break;
        case MMDB_DATA_TYPE_UINT128:
            handle_uint128(entry_data_list, z_value TSRMLS_CC);
            break;
        case MMDB_DATA_TYPE_INT32:
            ZVAL_LONG(z_value, entry_data_list->entry_data.int32);
            break;
        default:
            zend_throw_exception_ex(maxminddb_exception_ce,
                                    0 TSRMLS_CC,
                                    "Invalid data type arguments: %d",
                                    entry_data_list->entry_data.type);
            return NULL;
    }
    return entry_data_list;
}

static const MMDB_entry_data_list_s *
handle_map(const MMDB_entry_data_list_s *entry_data_list,
           zval *z_value TSRMLS_DC) {
    array_init(z_value);
    const uint32_t map_size = entry_data_list->entry_data.data_size;

    uint32_t i;
    for (i = 0; i < map_size && entry_data_list; i++) {
        entry_data_list = entry_data_list->next;

        char *key = estrndup((char *)entry_data_list->entry_data.utf8_string,
                             entry_data_list->entry_data.data_size);
        if (NULL == key) {
            zend_throw_exception_ex(maxminddb_exception_ce,
                                    0 TSRMLS_CC,
                                    "Invalid data type arguments");
            return NULL;
        }

        entry_data_list = entry_data_list->next;
        zval new_value;
        entry_data_list =
            handle_entry_data_list(entry_data_list, &new_value TSRMLS_CC);
        if (entry_data_list != NULL) {
            add_assoc_zval(z_value, key, &new_value);
        }
        efree(key);
    }
    return entry_data_list;
}

static const MMDB_entry_data_list_s *
handle_array(const MMDB_entry_data_list_s *entry_data_list,
             zval *z_value TSRMLS_DC) {
    const uint32_t size = entry_data_list->entry_data.data_size;

    array_init(z_value);

    uint32_t i;
    for (i = 0; i < size && entry_data_list; i++) {
        entry_data_list = entry_data_list->next;
        zval new_value;
        entry_data_list =
            handle_entry_data_list(entry_data_list, &new_value TSRMLS_CC);
        if (entry_data_list != NULL) {
            add_next_index_zval(z_value, &new_value);
        }
    }
    return entry_data_list;
}

static void handle_uint128(const MMDB_entry_data_list_s *entry_data_list,
                           zval *z_value TSRMLS_DC) {
    uint64_t high = 0;
    uint64_t low = 0;
#if MMDB_UINT128_IS_BYTE_ARRAY
    int i;
    for (i = 0; i < 8; i++) {
        high = (high << 8) | entry_data_list->entry_data.uint128[i];
    }

    for (i = 8; i < 16; i++) {
        low = (low << 8) | entry_data_list->entry_data.uint128[i];
    }
#else
    high = entry_data_list->entry_data.uint128 >> 64;
    low = (uint64_t)entry_data_list->entry_data.uint128;
#endif

    char *num_str;
    spprintf(&num_str, 0, "0x%016" PRIX64 "%016" PRIX64, high, low);
    CHECK_ALLOCATED(num_str);

    ZVAL_STRING(z_value, num_str);
    efree(num_str);
}

static void handle_uint32(const MMDB_entry_data_list_s *entry_data_list,
                          zval *z_value TSRMLS_DC) {
    uint32_t val = entry_data_list->entry_data.uint32;

#if LONG_MAX >= UINT32_MAX
    ZVAL_LONG(z_value, val);
    return;
#else
    if (val <= LONG_MAX) {
        ZVAL_LONG(z_value, val);
        return;
    }

    char *int_str;
    spprintf(&int_str, 0, "%" PRIu32, val);
    CHECK_ALLOCATED(int_str);

    ZVAL_STRING(z_value, int_str);
    efree(int_str);
#endif
}

static void handle_uint64(const MMDB_entry_data_list_s *entry_data_list,
                          zval *z_value TSRMLS_DC) {
    uint64_t val = entry_data_list->entry_data.uint64;

#if LONG_MAX >= UINT64_MAX
    ZVAL_LONG(z_value, val);
    return;
#else
    if (val <= LONG_MAX) {
        ZVAL_LONG(z_value, val);
        return;
    }

    char *int_str;
    spprintf(&int_str, 0, "%" PRIu64, val);
    CHECK_ALLOCATED(int_str);

    ZVAL_STRING(z_value, int_str);
    efree(int_str);
#endif
}

static void maxminddb_free_storage(free_obj_t *object TSRMLS_DC) {
    maxminddb_obj *obj =
        php_maxminddb_fetch_object((zend_object *)object TSRMLS_CC);
    if (obj->mmdb != NULL) {
        MMDB_close(obj->mmdb);
        efree(obj->mmdb);
    }

    zend_object_std_dtor(&obj->std TSRMLS_CC);
}

static zend_object *maxminddb_create_handler(zend_class_entry *type TSRMLS_DC) {
    maxminddb_obj *obj = (maxminddb_obj *)ecalloc(1, sizeof(maxminddb_obj));
    zend_object_std_init(&obj->std, type TSRMLS_CC);
    object_properties_init(&(obj->std), type);

    obj->std.handlers = &maxminddb_obj_handlers;

    return &obj->std;
}

/* clang-format off */
static zend_function_entry maxminddb_methods[] = {
    PHP_ME(MaxMind_Db_Reader, __construct, arginfo_maxminddbreader_construct,
           ZEND_ACC_PUBLIC | ZEND_ACC_CTOR)
    PHP_ME(MaxMind_Db_Reader, close, arginfo_maxminddbreader_void, ZEND_ACC_PUBLIC)
    PHP_ME(MaxMind_Db_Reader, get, arginfo_maxminddbreader_get,  ZEND_ACC_PUBLIC)
    PHP_ME(MaxMind_Db_Reader, getWithPrefixLen, arginfo_maxminddbreader_getWithPrefixLen,  ZEND_ACC_PUBLIC)
    PHP_ME(MaxMind_Db_Reader, metadata, arginfo_maxminddbreader_void, ZEND_ACC_PUBLIC)
    { NULL, NULL, NULL }
};
/* clang-format on */

ZEND_BEGIN_ARG_INFO_EX(arginfo_metadata_construct, 0, 0, 1)
ZEND_ARG_TYPE_INFO(0, metadata, IS_ARRAY, 0)
ZEND_END_ARG_INFO()

PHP_METHOD(MaxMind_Db_Reader_Metadata, __construct) {
    zval *object = NULL;
    zval *metadata_array = NULL;
    zend_long node_count = 0;
    zend_long record_size = 0;

    if (zend_parse_method_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                                     getThis(),
                                     "Oa",
                                     &object,
                                     metadata_ce,
                                     &metadata_array) == FAILURE) {
        return;
    }

    zval *tmp = NULL;
    if ((tmp = zend_hash_str_find(HASH_OF(metadata_array),
                                  "binary_format_major_version",
                                  sizeof("binary_format_major_version") - 1))) {
        zend_update_property(metadata_ce,
                             PROP_OBJ(object),
                             "binaryFormatMajorVersion",
                             sizeof("binaryFormatMajorVersion") - 1,
                             tmp);
    }

    if ((tmp = zend_hash_str_find(HASH_OF(metadata_array),
                                  "binary_format_minor_version",
                                  sizeof("binary_format_minor_version") - 1))) {
        zend_update_property(metadata_ce,
                             PROP_OBJ(object),
                             "binaryFormatMinorVersion",
                             sizeof("binaryFormatMinorVersion") - 1,
                             tmp);
    }

    if ((tmp = zend_hash_str_find(HASH_OF(metadata_array),
                                  "build_epoch",
                                  sizeof("build_epoch") - 1))) {
        zend_update_property(metadata_ce,
                             PROP_OBJ(object),
                             "buildEpoch",
                             sizeof("buildEpoch") - 1,
                             tmp);
    }

    if ((tmp = zend_hash_str_find(HASH_OF(metadata_array),
                                  "database_type",
                                  sizeof("database_type") - 1))) {
        zend_update_property(metadata_ce,
                             PROP_OBJ(object),
                             "databaseType",
                             sizeof("databaseType") - 1,
                             tmp);
    }

    if ((tmp = zend_hash_str_find(HASH_OF(metadata_array),
                                  "description",
                                  sizeof("description") - 1))) {
        zend_update_property(metadata_ce,
                             PROP_OBJ(object),
                             "description",
                             sizeof("description") - 1,
                             tmp);
    }

    if ((tmp = zend_hash_str_find(HASH_OF(metadata_array),
                                  "ip_version",
                                  sizeof("ip_version") - 1))) {
        zend_update_property(metadata_ce,
                             PROP_OBJ(object),
                             "ipVersion",
                             sizeof("ipVersion") - 1,
                             tmp);
    }

    if ((tmp = zend_hash_str_find(
             HASH_OF(metadata_array), "languages", sizeof("languages") - 1))) {
        zend_update_property(metadata_ce,
                             PROP_OBJ(object),
                             "languages",
                             sizeof("languages") - 1,
                             tmp);
    }

    if ((tmp = zend_hash_str_find(HASH_OF(metadata_array),
                                  "record_size",
                                  sizeof("record_size") - 1))) {
        zend_update_property(metadata_ce,
                             PROP_OBJ(object),
                             "recordSize",
                             sizeof("recordSize") - 1,
                             tmp);
        if (Z_TYPE_P(tmp) == IS_LONG) {
            record_size = Z_LVAL_P(tmp);
        }
    }

    if (record_size != 0) {
        zend_update_property_long(metadata_ce,
                                  PROP_OBJ(object),
                                  "nodeByteSize",
                                  sizeof("nodeByteSize") - 1,
                                  record_size / 4);
    }

    if ((tmp = zend_hash_str_find(HASH_OF(metadata_array),
                                  "node_count",
                                  sizeof("node_count") - 1))) {
        zend_update_property(metadata_ce,
                             PROP_OBJ(object),
                             "nodeCount",
                             sizeof("nodeCount") - 1,
                             tmp);
        if (Z_TYPE_P(tmp) == IS_LONG) {
            node_count = Z_LVAL_P(tmp);
        }
    }

    if (record_size != 0) {
        zend_update_property_long(metadata_ce,
                                  PROP_OBJ(object),
                                  "searchTreeSize",
                                  sizeof("searchTreeSize") - 1,
                                  record_size * node_count / 4);
    }
}

// clang-format off
static zend_function_entry metadata_methods[] = {
    PHP_ME(MaxMind_Db_Reader_Metadata, __construct, arginfo_metadata_construct, ZEND_ACC_PUBLIC | ZEND_ACC_CTOR)
    {NULL, NULL, NULL}
};
// clang-format on

PHP_MINIT_FUNCTION(maxminddb) {
    zend_class_entry ce;

    INIT_CLASS_ENTRY(ce, PHP_MAXMINDDB_READER_EX_NS, NULL);
    maxminddb_exception_ce =
        zend_register_internal_class_ex(&ce, zend_ce_exception);

    INIT_CLASS_ENTRY(ce, PHP_MAXMINDDB_READER_NS, maxminddb_methods);
    maxminddb_ce = zend_register_internal_class(&ce TSRMLS_CC);
    maxminddb_ce->create_object = maxminddb_create_handler;

    INIT_CLASS_ENTRY(ce, PHP_MAXMINDDB_METADATA_NS, metadata_methods);
    metadata_ce = zend_register_internal_class(&ce TSRMLS_CC);
    zend_declare_property_null(metadata_ce,
                               "binaryFormatMajorVersion",
                               sizeof("binaryFormatMajorVersion") - 1,
                               ZEND_ACC_PUBLIC);
    zend_declare_property_null(metadata_ce,
                               "binaryFormatMinorVersion",
                               sizeof("binaryFormatMinorVersion") - 1,
                               ZEND_ACC_PUBLIC);
    zend_declare_property_null(
        metadata_ce, "buildEpoch", sizeof("buildEpoch") - 1, ZEND_ACC_PUBLIC);
    zend_declare_property_null(metadata_ce,
                               "databaseType",
                               sizeof("databaseType") - 1,
                               ZEND_ACC_PUBLIC);
    zend_declare_property_null(
        metadata_ce, "description", sizeof("description") - 1, ZEND_ACC_PUBLIC);
    zend_declare_property_null(
        metadata_ce, "ipVersion", sizeof("ipVersion") - 1, ZEND_ACC_PUBLIC);
    zend_declare_property_null(
        metadata_ce, "languages", sizeof("languages") - 1, ZEND_ACC_PUBLIC);
    zend_declare_property_null(metadata_ce,
                               "nodeByteSize",
                               sizeof("nodeByteSize") - 1,
                               ZEND_ACC_PUBLIC);
    zend_declare_property_null(
        metadata_ce, "nodeCount", sizeof("nodeCount") - 1, ZEND_ACC_PUBLIC);
    zend_declare_property_null(
        metadata_ce, "recordSize", sizeof("recordSize") - 1, ZEND_ACC_PUBLIC);
    zend_declare_property_null(metadata_ce,
                               "searchTreeSize",
                               sizeof("searchTreeSize") - 1,
                               ZEND_ACC_PUBLIC);

    memcpy(&maxminddb_obj_handlers,
           zend_get_std_object_handlers(),
           sizeof(zend_object_handlers));
    maxminddb_obj_handlers.clone_obj = NULL;
    maxminddb_obj_handlers.offset = XtOffsetOf(maxminddb_obj, std);
    maxminddb_obj_handlers.free_obj = maxminddb_free_storage;
    zend_declare_class_constant_string(maxminddb_ce,
                                       "MMDB_LIB_VERSION",
                                       sizeof("MMDB_LIB_VERSION") - 1,
                                       MMDB_lib_version() TSRMLS_CC);

    return SUCCESS;
}

static PHP_MINFO_FUNCTION(maxminddb) {
    php_info_print_table_start();

    php_info_print_table_row(2, "MaxMind DB Reader", "enabled");
    php_info_print_table_row(
        2, "maxminddb extension version", PHP_MAXMINDDB_VERSION);
    php_info_print_table_row(
        2, "libmaxminddb library version", MMDB_lib_version());

    php_info_print_table_end();
}

zend_module_entry maxminddb_module_entry = {STANDARD_MODULE_HEADER,
                                            PHP_MAXMINDDB_EXTNAME,
                                            NULL,
                                            PHP_MINIT(maxminddb),
                                            NULL,
                                            NULL,
                                            NULL,
                                            PHP_MINFO(maxminddb),
                                            PHP_MAXMINDDB_VERSION,
                                            STANDARD_MODULE_PROPERTIES};

#ifdef COMPILE_DL_MAXMINDDB
ZEND_GET_MODULE(maxminddb)
#endif
