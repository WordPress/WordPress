if (typeof FormData === 'undefined' || !FormData.prototype.keys) {
  const global = typeof window === 'object'
    ? window : typeof self === 'object'
    ? self : this

  // keep a reference to native implementation
  const _FormData = global.FormData

  // To be monkey patched
  const _send = global.XMLHttpRequest && global.XMLHttpRequest.prototype.send
  const _fetch = global.Request && global.fetch

  // Unable to patch Request constructor correctly
  // const _Request = global.Request
  // only way is to use ES6 class extend
  // https://github.com/babel/babel/issues/1966

  const stringTag = global.Symbol && Symbol.toStringTag
  const map = new WeakMap
  const wm = o => map.get(o)
  const arrayFrom = Array.from || (obj => [].slice.call(obj))

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
    new File([], '')
  } catch (a) {
    global.File = function(b, d, c) {
      const blob = new Blob(b, c)
      const t = c && void 0 !== c.lastModified ? new Date(c.lastModified) : new Date

      Object.defineProperties(blob, {
        name: {
          value: d
        },
        lastModifiedDate: {
          value: t
        },
        lastModified: {
          value: +t
        },
        toString: {
          value() {
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

  /**
   * @implements {Iterable}
   */
  class FormDataPolyfill {

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


    /**
     * Iterate over all fields as [name, value]
     *
     * @return {Iterator}
     */
    *entries() {
      const map = wm(this)

      for (let name in map)
        for (let value of map[name])
          yield [name, normalizeValue(value)]
    }

    /**
     * Iterate over all fields
     *
     * @param   {Function}  callback  Executed for each item with parameters (value, name, thisArg)
     * @param   {Object=}   thisArg   `this` context for callback function
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


    /**
     * Iterate over all fields name
     *
     * @return {Iterator}
     */
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


    /**
     * Iterate over all fields
     *
     * @return {Iterator}
     */
    *values() {
      for (let [name, value] of this)
        yield value
    }


    /**
     * Return a native (perhaps degraded) FormData with only a `append` method
     * Can throw if it's not supported
     *
     * @return {FormData}
     */
    ['_asNative']() {
      const fd = new _FormData

      for (let [name, value] of this)
        fd.append(name, value)

      return fd
    }


    /**
     * [_blob description]
     *
     * @return {Blob} [description]
     */
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


    /**
     * The class itself is iterable
     * alias for formdata.entries()
     *
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
      return '[object FormData]'
    }
  }


  if (stringTag) {
    /**
     * Create the default string description.
     * It is accessed internally by the Object.prototype.toString().
     *
     * @return {String} FormData
     */
    FormDataPolyfill.prototype[stringTag] = 'FormData'
  }

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
        _send.call(this, blob)
      } else {
        _send.call(this, data)
      }
    }
  }

  // Patch fetch's function to call _blob transparently
  if (_fetch) {
    const _fetch = global.fetch

    global.fetch = function(input, init) {
      if (init && init.body && init.body instanceof FormDataPolyfill) {
        init.body = init.body['_blob']()
      }

      return _fetch(input, init)
    }
  }

  global['FormData'] = FormDataPolyfill
}
