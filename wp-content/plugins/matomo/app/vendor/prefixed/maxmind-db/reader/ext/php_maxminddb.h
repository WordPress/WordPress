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

#include <zend_interfaces.h>

#ifndef PHP_MAXMINDDB_H
#define PHP_MAXMINDDB_H 1
#define PHP_MAXMINDDB_VERSION "1.12.0"
#define PHP_MAXMINDDB_EXTNAME "maxminddb"

extern zend_module_entry maxminddb_module_entry;
#define phpext_maxminddb_ptr &maxminddb_module_entry

#endif
