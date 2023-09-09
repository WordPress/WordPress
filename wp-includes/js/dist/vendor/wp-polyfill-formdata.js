<<<<<<< HEAD
/* formdata-polyfill. MIT License. Jimmy Wärting <https://jimmy.warting.se/opensource> */

/* global FormData self Blob File */
/* eslint-disable no-inner-declarations */

if (typeof Blob !== 'undefined' && (typeof FormData === 'undefined' || !FormData.prototype.keys)) {
  const global = typeof globalThis === 'object'
    ? globalThis
    : typeof window === 'object'
      ? window
      : typeof self === 'object' ? self : this
=======
if (typeof FormData === 'undefined' || !FormData.prototype.keys) {
  const global = typeof window === 'object'
    ? window : typeof self === 'object'
    ? self : this
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  // keep a reference to native implementation
  const _FormData = global.FormData

  // To be monkey patched
  const _send = global.XMLHttpRequest && global.XMLHttpRequest.prototype.send
  const _fetch = global.Request && global.fetch
<<<<<<< HEAD
  const _sendBeacon = global.navigator && global.navigator.sendBeacon
  // Might be a worker thread...
  const _match = global.Element && global.Element.prototype

  // Unable to patch Request/Response constructor correctly #109
=======

  // Unable to patch Request constructor correctly
  // const _Request = global.Request
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  // only way is to use ES6 class extend
  // https://github.com/babel/babel/issues/1966

  const stringTag = global.Symbol && Symbol.toStringTag
<<<<<<< HEAD
=======
  const map = new WeakMap
  const wm = o => map.get(o)
  const arrayFrom = Array.from || (obj => [].slice.call(obj))
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  // Add missing stringTags to blob and files
  if (stringTag) {
    if (!Blob.prototype[stringTag]) {
      Blob.prototype[stringTag] = 'Blob'
    }

    if ('File' in global && !File.prototype[stringTag]) {
      File.prototype[stringTag] = 'File'
    }
  }

  // Fix so you can construct your own File
  try {
<<<<<<< HEAD
    new File([], '') // eslint-disable-line
  } catch (a) {
    global.File = function File (b, d, c) {
      const blob = new Blob(b, c || {})
      const t = c && void 0 !== c.lastModified ? new Date(c.lastModified) : new Date()
=======
    new File([], '')
  } catch (a) {
    global.File = function(b, d, c) {
      const blob = new Blob(b, c)
      const t = c && void 0 !== c.lastModified ? new Date(c.lastModified) : new Date
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

      Object.defineProperties(blob, {
        name: {
          value: d
        },
<<<<<<< HEAD
=======
        lastModifiedDate: {
          value: t
        },
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
        lastModified: {
          value: +t
        },
        toString: {
<<<<<<< HEAD
          value () {
=======
          value() {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
            return '[object File]'
          }
        }
      })

      if (stringTag) {
        Object.defineProperty(blob, stringTag, {
          value: 'File'
        })
      }

      return blob
    }
  }

<<<<<<< HEAD
  function ensureArgs (args, expected) {
    if (args.length < expected) {
      throw new TypeError(`${expected} argument required, but only ${args.length} present.`)
    }
  }

  /**
   * @param {string} name
   * @param {string | undefined} filename
   * @returns {[string, File|string]}
   */
  function normalizeArgs (name, value, filename) {
    if (value instanceof Blob) {
      filename = filename !== undefined
      ? String(filename + '')
      : typeof value.name === 'string'
      ? value.name
      : 'blob'

      if (value.name !== filename || Object.prototype.toString.call(value) === '[object Blob]') {
        value = new File([value], filename)
      }
      return [String(name), value]
    }
    return [String(name), String(value)]
  }

  // normalize line feeds for textarea
  // https://html.spec.whatwg.org/multipage/form-elements.html#textarea-line-break-normalisation-transformation
  function normalizeLinefeeds (value) {
    return value.replace(/\r?\n|\r/g, '\r\n')
  }

  /**
   * @template T
   * @param {ArrayLike<T>} arr
   * @param {{ (elm: T): void; }} cb
   */
  function each (arr, cb) {
    for (let i = 0; i < arr.length; i++) {
      cb(arr[i])
    }
  }

  const escape = str => str.replace(/\n/g, '%0A').replace(/\r/g, '%0D').replace(/"/g, '%22')
=======
  function normalizeValue([value, filename]) {
    if (value instanceof Blob)
      // Should always returns a new File instance
      // console.assert(fd.get(x) !== fd.get(x))
      value = new File([value], filename, {
        type: value.type,
        lastModified: value.lastModified
      })

    return value
  }

  function stringify(name) {
    if (!arguments.length)
      throw new TypeError('1 argument required, but only 0 present.')

    return [name + '']
  }

  function normalizeArgs(name, value, filename) {
    if (arguments.length < 2)
      throw new TypeError(
        `2 arguments required, but only ${arguments.length} present.`
      )

    return value instanceof Blob
      // normalize name and filename if adding an attachment
      ? [name + '', value, filename !== undefined
        ? filename + '' // Cast filename to string if 3th arg isn't undefined
        : typeof value.name === 'string' // if name prop exist
          ? value.name // Use File.name
          : 'blob'] // otherwise fallback to Blob

      // If no attachment, just cast the args to strings
      : [name + '', value + '']
  }
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  /**
   * @implements {Iterable}
   */
  class FormDataPolyfill {
<<<<<<< HEAD
    /**
     * FormData class
     *
     * @param {HTMLFormElement=} form
     */
    constructor (form) {
      /** @type {[string, string|File][]} */
      this._data = []

      const self = this
      form && each(form.elements, (/** @type {HTMLInputElement} */ elm) => {
        if (
          !elm.name ||
          elm.disabled ||
          elm.type === 'submit' ||
          elm.type === 'button' ||
          elm.matches('form fieldset[disabled] *')
        ) return

        if (elm.type === 'file') {
          const files = elm.files && elm.files.length
            ? elm.files
            : [new File([], '', { type: 'application/octet-stream' })] // #78

          each(files, file => {
            self.append(elm.name, file)
          })
        } else if (elm.type === 'select-multiple' || elm.type === 'select-one') {
          each(elm.options, opt => {
            !opt.disabled && opt.selected && self.append(elm.name, opt.value)
          })
        } else if (elm.type === 'checkbox' || elm.type === 'radio') {
          if (elm.checked) self.append(elm.name, elm.value)
        } else {
          const value = elm.type === 'textarea' ? normalizeLinefeeds(elm.value) : elm.value
          self.append(elm.name, value)
        }
      })
    }

    /**
     * Append a field
     *
     * @param   {string}           name      field name
     * @param   {string|Blob|File} value     string / blob / file
     * @param   {string=}          filename  filename to use with blob
     * @return  {undefined}
     */
    append (name, value, filename) {
      ensureArgs(arguments, 2)
      this._data.push(normalizeArgs(name, value, filename))
    }

    /**
     * Delete all fields values given name
     *
     * @param   {string}  name  Field name
     * @return  {undefined}
     */
    delete (name) {
      ensureArgs(arguments, 1)
      const result = []
      name = String(name)

      each(this._data, entry => {
        entry[0] !== name && result.push(entry)
      })

      this._data = result
    }

=======

    /**
     * FormData class
     *
     * @param {HTMLElement=} form
     */
    constructor(form) {
      map.set(this, Object.create(null))

      if (!form)
        return this

      for (let elm of arrayFrom(form.elements)) {
        if (!elm.name || elm.disabled) continue

        if (elm.type === 'file')
          for (let file of arrayFrom(elm.files || []))
            this.append(elm.name, file)
        else if (elm.type === 'select-multiple' || elm.type === 'select-one')
          for (let opt of arrayFrom(elm.options))
            !opt.disabled && opt.selected && this.append(elm.name, opt.value)
        else if (elm.type === 'checkbox' || elm.type === 'radio') {
          if (elm.checked) this.append(elm.name, elm.value)
        } else
          this.append(elm.name, elm.value)
      }
    }


    /**
     * Append a field
     *
     * @param   {String}           name      field name
     * @param   {String|Blob|File} value     string / blob / file
     * @param   {String=}          filename  filename to use with blob
     * @return  {Undefined}
     */
    append(name, value, filename) {
      const map = wm(this)

      if (!map[name])
        map[name] = []

      map[name].push([value, filename])
    }


    /**
     * Delete all fields values given name
     *
     * @param   {String}  name  Field name
     * @return  {Undefined}
     */
    delete(name) {
      delete wm(this)[name]
    }


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    /**
     * Iterate over all fields as [name, value]
     *
     * @return {Iterator}
     */
<<<<<<< HEAD
    * entries () {
      for (var i = 0; i < this._data.length; i++) {
        yield this._data[i]
      }
=======
    *entries() {
      const map = wm(this)

      for (let name in map)
        for (let value of map[name])
          yield [name, normalizeValue(value)]
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    }

    /**
     * Iterate over all fields
     *
     * @param   {Function}  callback  Executed for each item with parameters (value, name, thisArg)
     * @param   {Object=}   thisArg   `this` context for callback function
<<<<<<< HEAD
     */
    forEach (callback, thisArg) {
      ensureArgs(arguments, 1)
      for (const [name, value] of this) {
        callback.call(thisArg, value, name, this)
      }
    }

    /**
     * Return first field value given name
     * or null if non existent
     *
     * @param   {string}  name      Field name
     * @return  {string|File|null}  value Fields value
     */
    get (name) {
      ensureArgs(arguments, 1)
      const entries = this._data
      name = String(name)
      for (let i = 0; i < entries.length; i++) {
        if (entries[i][0] === name) {
          return entries[i][1]
        }
      }
      return null
    }

    /**
     * Return all fields values given name
     *
     * @param   {string}  name  Fields name
     * @return  {Array}         [{String|File}]
     */
    getAll (name) {
      ensureArgs(arguments, 1)
      const result = []
      name = String(name)
      each(this._data, data => {
        data[0] === name && result.push(data[1])
      })

      return result
    }

    /**
     * Check for field name existence
     *
     * @param   {string}   name  Field name
     * @return  {boolean}
     */
    has (name) {
      ensureArgs(arguments, 1)
      name = String(name)
      for (let i = 0; i < this._data.length; i++) {
        if (this._data[i][0] === name) {
          return true
        }
      }
      return false
    }

=======
     * @return  {Undefined}
     */
    forEach(callback, thisArg) {
      for (let [name, value] of this)
        callback.call(thisArg, value, name, this)
    }


    /**
     * Return first field value given name
     * or null if non existen
     *
     * @param   {String}  name      Field name
     * @return  {String|File|null}  value Fields value
     */
    get(name) {
      const map = wm(this)
      return map[name] ? normalizeValue(map[name][0]) : null
    }


    /**
     * Return all fields values given name
     *
     * @param   {String}  name  Fields name
     * @return  {Array}         [{String|File}]
     */
    getAll(name) {
      return (wm(this)[name] || []).map(normalizeValue)
    }


    /**
     * Check for field name existence
     *
     * @param   {String}   name  Field name
     * @return  {boolean}
     */
    has(name) {
      return name in wm(this)
    }


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    /**
     * Iterate over all fields name
     *
     * @return {Iterator}
     */
<<<<<<< HEAD
    * keys () {
      for (const [name] of this) {
        yield name
      }
    }

    /**
     * Overwrite all values given name
     *
     * @param   {string}    name      Filed name
     * @param   {string}    value     Field value
     * @param   {string=}   filename  Filename (optional)
     */
    set (name, value, filename) {
      ensureArgs(arguments, 2)
      name = String(name)
      /** @type {[string, string|File][]} */
      const result = []
      const args = normalizeArgs(name, value, filename)
      let replace = true

      // - replace the first occurrence with same name
      // - discards the remaining with same name
      // - while keeping the same order items where added
      each(this._data, data => {
        data[0] === name
          ? replace && (replace = !result.push(args))
          : result.push(data)
      })

      replace && result.push(args)

      this._data = result
    }

=======
    *keys() {
      for (let [name] of this)
        yield name
    }


    /**
     * Overwrite all values given name
     *
     * @param   {String}    name      Filed name
     * @param   {String}    value     Field value
     * @param   {String=}   filename  Filename (optional)
     * @return  {Undefined}
     */
    set(name, value, filename) {
      wm(this)[name] = [[value, filename]]
    }


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    /**
     * Iterate over all fields
     *
     * @return {Iterator}
     */
<<<<<<< HEAD
    * values () {
      for (const [, value] of this) {
        yield value
      }
    }

=======
    *values() {
      for (let [name, value] of this)
        yield value
    }


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    /**
     * Return a native (perhaps degraded) FormData with only a `append` method
     * Can throw if it's not supported
     *
     * @return {FormData}
     */
<<<<<<< HEAD
    ['_asNative'] () {
      const fd = new _FormData()

      for (const [name, value] of this) {
        fd.append(name, value)
      }
=======
    ['_asNative']() {
      const fd = new _FormData

      for (let [name, value] of this)
        fd.append(name, value)
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

      return fd
    }

<<<<<<< HEAD
=======

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    /**
     * [_blob description]
     *
     * @return {Blob} [description]
     */
<<<<<<< HEAD
    ['_blob'] () {
        const boundary = '----formdata-polyfill-' + Math.random(),
          chunks = [],
          p = `--${boundary}\r\nContent-Disposition: form-data; name="`
        this.forEach((value, name) => typeof value == 'string'
          ? chunks.push(p + escape(normalizeLinefeeds(name)) + `"\r\n\r\n${normalizeLinefeeds(value)}\r\n`)
          : chunks.push(p + escape(normalizeLinefeeds(name)) + `"; filename="${escape(value.name)}"\r\nContent-Type: ${value.type||"application/octet-stream"}\r\n\r\n`, value, `\r\n`))
        chunks.push(`--${boundary}--`)
        return new Blob(chunks, {
          type: "multipart/form-data; boundary=" + boundary
        })
    }

=======
    ['_blob']() {
      const boundary = '----formdata-polyfill-' + Math.random()
      const chunks = []

      for (let [name, value] of this) {
        chunks.push(`--${boundary}\r\n`)

        if (value instanceof Blob) {
          chunks.push(
            `Content-Disposition: form-data; name="${name}"; filename="${value.name}"\r\n`,
            `Content-Type: ${value.type || 'application/octet-stream'}\r\n\r\n`,
            value,
            '\r\n'
          )
        } else {
          chunks.push(
            `Content-Disposition: form-data; name="${name}"\r\n\r\n${value}\r\n`
          )
        }
      }

      chunks.push(`--${boundary}--`)

      return new Blob(chunks, {type: 'multipart/form-data; boundary=' + boundary})
    }


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    /**
     * The class itself is iterable
     * alias for formdata.entries()
     *
<<<<<<< HEAD
     * @return {Iterator}
     */
    [Symbol.iterator] () {
      return this.entries()
    }

    /**
     * Create the default string description.
     *
     * @return  {string} [object FormData]
     */
    toString () {
=======
     * @return  {Iterator}
     */
    [Symbol.iterator]() {
      return this.entries()
    }


    /**
     * Create the default string description.
     *
     * @return  {String} [object FormData]
     */
    toString() {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      return '[object FormData]'
    }
  }

<<<<<<< HEAD
  if (_match && !_match.matches) {
    _match.matches =
      _match.matchesSelector ||
      _match.mozMatchesSelector ||
      _match.msMatchesSelector ||
      _match.oMatchesSelector ||
      _match.webkitMatchesSelector ||
      function (s) {
        var matches = (this.document || this.ownerDocument).querySelectorAll(s)
        var i = matches.length
        while (--i >= 0 && matches.item(i) !== this) {}
        return i > -1
      }
  }
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (stringTag) {
    /**
     * Create the default string description.
     * It is accessed internally by the Object.prototype.toString().
<<<<<<< HEAD
=======
     *
     * @return {String} FormData
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
     */
    FormDataPolyfill.prototype[stringTag] = 'FormData'
  }

<<<<<<< HEAD
  // Patch xhr's send method to call _blob transparently
  if (_send) {
    const setRequestHeader = global.XMLHttpRequest.prototype.setRequestHeader

    global.XMLHttpRequest.prototype.setRequestHeader = function (name, value) {
      setRequestHeader.call(this, name, value)
      if (name.toLowerCase() === 'content-type') this._hasContentType = true
    }

    global.XMLHttpRequest.prototype.send = function (data) {
      // need to patch send b/c old IE don't send blob's type (#44)
      if (data instanceof FormDataPolyfill) {
        const blob = data['_blob']()
        if (!this._hasContentType) this.setRequestHeader('Content-Type', blob.type)
=======
  const decorations = [
    ['append', normalizeArgs],
    ['delete', stringify],
    ['get',    stringify],
    ['getAll', stringify],
    ['has',    stringify],
    ['set',    normalizeArgs]
  ]

  decorations.forEach(arr => {
    const orig = FormDataPolyfill.prototype[arr[0]]
    FormDataPolyfill.prototype[arr[0]] = function() {
      return orig.apply(this, arr[1].apply(this, arrayFrom(arguments)))
    }
  })

  // Patch xhr's send method to call _blob transparently
  if (_send) {
      XMLHttpRequest.prototype.send = function(data) {
      // I would check if Content-Type isn't already set
      // But xhr lacks getRequestHeaders functionallity
      // https://github.com/jimmywarting/FormData/issues/44
      if (data instanceof FormDataPolyfill) {
        const blob = data['_blob']()
        this.setRequestHeader('Content-Type', blob.type)
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
        _send.call(this, blob)
      } else {
        _send.call(this, data)
      }
    }
  }

  // Patch fetch's function to call _blob transparently
  if (_fetch) {
<<<<<<< HEAD
    global.fetch = function (input, init) {
=======
    const _fetch = global.fetch

    global.fetch = function(input, init) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      if (init && init.body && init.body instanceof FormDataPolyfill) {
        init.body = init.body['_blob']()
      }

<<<<<<< HEAD
      return _fetch.call(this, input, init)
    }
  }

  // Patch navigator.sendBeacon to use native FormData
  if (_sendBeacon) {
    global.navigator.sendBeacon = function (url, data) {
      if (data instanceof FormDataPolyfill) {
        data = data['_asNative']()
      }
      return _sendBeacon.call(this, url, data)
=======
      return _fetch(input, init)
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    }
  }

  global['FormData'] = FormDataPolyfill
}
