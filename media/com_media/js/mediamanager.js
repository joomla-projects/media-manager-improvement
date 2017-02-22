<<<<<<< HEAD
(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (process){
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.

// resolves . and .. elements in a path array with directory names there
// must be no slashes, empty elements, or device names (c:\) in the array
// (so also no leading and trailing slashes - it does not distinguish
// relative and absolute paths)
function normalizeArray(parts, allowAboveRoot) {
  // if the path tries to go above the root, `up` ends up > 0
  var up = 0;
  for (var i = parts.length - 1; i >= 0; i--) {
    var last = parts[i];
    if (last === '.') {
      parts.splice(i, 1);
    } else if (last === '..') {
      parts.splice(i, 1);
      up++;
    } else if (up) {
      parts.splice(i, 1);
      up--;
    }
  }

  // if the path is allowed to go above the root, restore leading ..s
  if (allowAboveRoot) {
    for (; up--; up) {
      parts.unshift('..');
    }
  }

  return parts;
}

// Split a filename into [root, dir, basename, ext], unix version
// 'root' is just a slash, or nothing.
var splitPathRe =
    /^(\/?|)([\s\S]*?)((?:\.{1,2}|[^\/]+?|)(\.[^.\/]*|))(?:[\/]*)$/;
var splitPath = function(filename) {
  return splitPathRe.exec(filename).slice(1);
};

// path.resolve([from ...], to)
// posix version
exports.resolve = function() {
  var resolvedPath = '',
      resolvedAbsolute = false;

  for (var i = arguments.length - 1; i >= -1 && !resolvedAbsolute; i--) {
    var path = (i >= 0) ? arguments[i] : process.cwd();

    // Skip empty and invalid entries
    if (typeof path !== 'string') {
      throw new TypeError('Arguments to path.resolve must be strings');
    } else if (!path) {
      continue;
    }

    resolvedPath = path + '/' + resolvedPath;
    resolvedAbsolute = path.charAt(0) === '/';
  }

  // At this point the path should be resolved to a full absolute path, but
  // handle relative paths to be safe (might happen when process.cwd() fails)

  // Normalize the path
  resolvedPath = normalizeArray(filter(resolvedPath.split('/'), function(p) {
    return !!p;
  }), !resolvedAbsolute).join('/');

  return ((resolvedAbsolute ? '/' : '') + resolvedPath) || '.';
};

// path.normalize(path)
// posix version
exports.normalize = function(path) {
  var isAbsolute = exports.isAbsolute(path),
      trailingSlash = substr(path, -1) === '/';

  // Normalize the path
  path = normalizeArray(filter(path.split('/'), function(p) {
    return !!p;
  }), !isAbsolute).join('/');

  if (!path && !isAbsolute) {
    path = '.';
  }
  if (path && trailingSlash) {
    path += '/';
  }

  return (isAbsolute ? '/' : '') + path;
};

// posix version
exports.isAbsolute = function(path) {
  return path.charAt(0) === '/';
};

// posix version
exports.join = function() {
  var paths = Array.prototype.slice.call(arguments, 0);
  return exports.normalize(filter(paths, function(p, index) {
    if (typeof p !== 'string') {
      throw new TypeError('Arguments to path.join must be strings');
    }
    return p;
  }).join('/'));
};


// path.relative(from, to)
// posix version
exports.relative = function(from, to) {
  from = exports.resolve(from).substr(1);
  to = exports.resolve(to).substr(1);

  function trim(arr) {
    var start = 0;
    for (; start < arr.length; start++) {
      if (arr[start] !== '') break;
    }

    var end = arr.length - 1;
    for (; end >= 0; end--) {
      if (arr[end] !== '') break;
    }

    if (start > end) return [];
    return arr.slice(start, end - start + 1);
  }

  var fromParts = trim(from.split('/'));
  var toParts = trim(to.split('/'));

  var length = Math.min(fromParts.length, toParts.length);
  var samePartsLength = length;
  for (var i = 0; i < length; i++) {
    if (fromParts[i] !== toParts[i]) {
      samePartsLength = i;
      break;
    }
  }

  var outputParts = [];
  for (var i = samePartsLength; i < fromParts.length; i++) {
    outputParts.push('..');
  }

  outputParts = outputParts.concat(toParts.slice(samePartsLength));

  return outputParts.join('/');
};

exports.sep = '/';
exports.delimiter = ':';

exports.dirname = function(path) {
  var result = splitPath(path),
      root = result[0],
      dir = result[1];

  if (!root && !dir) {
    // No dirname whatsoever
    return '.';
  }

  if (dir) {
    // It has a dirname, strip trailing slash
    dir = dir.substr(0, dir.length - 1);
  }

  return root + dir;
};


exports.basename = function(path, ext) {
  var f = splitPath(path)[2];
  // TODO: make this comparison case-insensitive on windows?
  if (ext && f.substr(-1 * ext.length) === ext) {
    f = f.substr(0, f.length - ext.length);
  }
  return f;
};


exports.extname = function(path) {
  return splitPath(path)[3];
};

function filter (xs, f) {
    if (xs.filter) return xs.filter(f);
    var res = [];
    for (var i = 0; i < xs.length; i++) {
        if (f(xs[i], i, xs)) res.push(xs[i]);
    }
    return res;
}

// String.prototype.substr - negative index don't work in IE8
var substr = 'ab'.substr(-1) === 'b'
    ? function (str, start, len) { return str.substr(start, len) }
    : function (str, start, len) {
        if (start < 0) start = str.length + start;
        return str.substr(start, len);
    }
;

}).call(this,require('_process'))
},{"_process":2}],2:[function(require,module,exports){
// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };

},{}],3:[function(require,module,exports){
'use strict';

var Vue = require('vue');
Vue = 'default' in Vue ? Vue['default'] : Vue;

var version = '2.1.0';

var compatible = (/^2\./).test(Vue.version);
if (!compatible) {
  Vue.util.warn('VueFocus ' + version + ' only supports Vue 2.x, and does not support Vue ' + Vue.version);
}

var focus = {
  inserted: function(el, binding) {
    if (binding.value) el.focus();
    else el.blur();
  },

  componentUpdated: function(el, binding) {
    if (binding.modifiers.lazy) {
      if (Boolean(binding.value) === Boolean(binding.oldValue)) {
        return;
      }
    }

    if (binding.value) el.focus();
    else el.blur();
  },
};

var mixin = {
  directives: {
    focus: focus,
  },
};

exports.version = version;
exports.focus = focus;
exports.mixin = mixin;
},{"vue":5}],4:[function(require,module,exports){
var Vue // late bind
var version
var map = window.__VUE_HOT_MAP__ = Object.create(null)
var installed = false
var isBrowserify = false
var initHookName = 'beforeCreate'

exports.install = function (vue, browserify) {
  if (installed) return
  installed = true

  Vue = vue.__esModule ? vue.default : vue
  version = Vue.version.split('.').map(Number)
  isBrowserify = browserify

  // compat with < 2.0.0-alpha.7
  if (Vue.config._lifecycleHooks.indexOf('init') > -1) {
    initHookName = 'init'
  }

  exports.compatible = version[0] >= 2
  if (!exports.compatible) {
    console.warn(
      '[HMR] You are using a version of vue-hot-reload-api that is ' +
      'only compatible with Vue.js core ^2.0.0.'
    )
    return
  }
}

/**
 * Create a record for a hot module, which keeps track of its constructor
 * and instances
 *
 * @param {String} id
 * @param {Object} options
 */

exports.createRecord = function (id, options) {
  var Ctor = null
  if (typeof options === 'function') {
    Ctor = options
    options = Ctor.options
  }
  makeOptionsHot(id, options)
  map[id] = {
    Ctor: Vue.extend(options),
    instances: []
  }
}

/**
 * Make a Component options object hot.
 *
 * @param {String} id
 * @param {Object} options
 */

function makeOptionsHot (id, options) {
  injectHook(options, initHookName, function () {
    map[id].instances.push(this)
  })
  injectHook(options, 'beforeDestroy', function () {
    var instances = map[id].instances
    instances.splice(instances.indexOf(this), 1)
  })
}

/**
 * Inject a hook to a hot reloadable component so that
 * we can keep track of it.
 *
 * @param {Object} options
 * @param {String} name
 * @param {Function} hook
 */

function injectHook (options, name, hook) {
  var existing = options[name]
  options[name] = existing
    ? Array.isArray(existing)
      ? existing.concat(hook)
      : [existing, hook]
    : [hook]
}

function tryWrap (fn) {
  return function (id, arg) {
    try { fn(id, arg) } catch (e) {
      console.error(e)
      console.warn('Something went wrong during Vue component hot-reload. Full reload required.')
    }
  }
}

exports.rerender = tryWrap(function (id, options) {
  var record = map[id]
  if (typeof options === 'function') {
    options = options.options
  }
  record.Ctor.options.render = options.render
  record.Ctor.options.staticRenderFns = options.staticRenderFns
  record.instances.slice().forEach(function (instance) {
    instance.$options.render = options.render
    instance.$options.staticRenderFns = options.staticRenderFns
    instance._staticTrees = [] // reset static trees
    instance.$forceUpdate()
  })
})

exports.reload = tryWrap(function (id, options) {
  if (typeof options === 'function') {
    options = options.options
  }
  makeOptionsHot(id, options)
  var record = map[id]
  if (version[1] < 2) {
    // preserve pre 2.2 behavior for global mixin handling
    record.Ctor.extendOptions = options
  }
  var newCtor = record.Ctor.super.extend(options)
  record.Ctor.options = newCtor.options
  record.Ctor.cid = newCtor.cid
  record.Ctor.prototype = newCtor.prototype
  if (newCtor.release) {
    // temporary global mixin strategy used in < 2.0.0-alpha.6
    newCtor.release()
  }
  record.instances.slice().forEach(function (instance) {
    if (instance.$vnode && instance.$vnode.context) {
      instance.$vnode.context.$forceUpdate()
    } else {
      console.warn('Root or manually mounted instance modified. Full reload required.')
    }
  })
})

},{}],5:[function(require,module,exports){
(function (process,global){
/*!
 * Vue.js v2.2.6
 * (c) 2014-2017 Evan You
 * Released under the MIT License.
 */
'use strict';

/*  */

/**
 * Convert a value to a string that is actually rendered.
 */
function _toString (val) {
  return val == null
    ? ''
    : typeof val === 'object'
      ? JSON.stringify(val, null, 2)
      : String(val)
}

/**
 * Convert a input value to a number for persistence.
 * If the conversion fails, return original string.
 */
function toNumber (val) {
  var n = parseFloat(val);
  return isNaN(n) ? val : n
}

/**
 * Make a map and return a function for checking if a key
 * is in that map.
 */
function makeMap (
  str,
  expectsLowerCase
) {
  var map = Object.create(null);
  var list = str.split(',');
  for (var i = 0; i < list.length; i++) {
    map[list[i]] = true;
  }
  return expectsLowerCase
    ? function (val) { return map[val.toLowerCase()]; }
    : function (val) { return map[val]; }
}

/**
 * Check if a tag is a built-in tag.
 */
var isBuiltInTag = makeMap('slot,component', true);

/**
 * Remove an item from an array
 */
function remove (arr, item) {
  if (arr.length) {
    var index = arr.indexOf(item);
    if (index > -1) {
      return arr.splice(index, 1)
    }
  }
}

/**
 * Check whether the object has the property.
 */
var hasOwnProperty = Object.prototype.hasOwnProperty;
function hasOwn (obj, key) {
  return hasOwnProperty.call(obj, key)
}

/**
 * Check if value is primitive
 */
function isPrimitive (value) {
  return typeof value === 'string' || typeof value === 'number'
}

/**
 * Create a cached version of a pure function.
 */
function cached (fn) {
  var cache = Object.create(null);
  return (function cachedFn (str) {
    var hit = cache[str];
    return hit || (cache[str] = fn(str))
  })
}

/**
 * Camelize a hyphen-delimited string.
 */
var camelizeRE = /-(\w)/g;
var camelize = cached(function (str) {
  return str.replace(camelizeRE, function (_, c) { return c ? c.toUpperCase() : ''; })
});

/**
 * Capitalize a string.
 */
var capitalize = cached(function (str) {
  return str.charAt(0).toUpperCase() + str.slice(1)
});

/**
 * Hyphenate a camelCase string.
 */
var hyphenateRE = /([^-])([A-Z])/g;
var hyphenate = cached(function (str) {
  return str
    .replace(hyphenateRE, '$1-$2')
    .replace(hyphenateRE, '$1-$2')
    .toLowerCase()
});

/**
 * Simple bind, faster than native
 */
function bind (fn, ctx) {
  function boundFn (a) {
    var l = arguments.length;
    return l
      ? l > 1
        ? fn.apply(ctx, arguments)
        : fn.call(ctx, a)
      : fn.call(ctx)
  }
  // record original fn length
  boundFn._length = fn.length;
  return boundFn
}

/**
 * Convert an Array-like object to a real Array.
 */
function toArray (list, start) {
  start = start || 0;
  var i = list.length - start;
  var ret = new Array(i);
  while (i--) {
    ret[i] = list[i + start];
  }
  return ret
}

/**
 * Mix properties into target object.
 */
function extend (to, _from) {
  for (var key in _from) {
    to[key] = _from[key];
  }
  return to
}

/**
 * Quick object check - this is primarily used to tell
 * Objects from primitive values when we know the value
 * is a JSON-compliant type.
 */
function isObject (obj) {
  return obj !== null && typeof obj === 'object'
}

/**
 * Strict object type check. Only returns true
 * for plain JavaScript objects.
 */
var toString = Object.prototype.toString;
var OBJECT_STRING = '[object Object]';
function isPlainObject (obj) {
  return toString.call(obj) === OBJECT_STRING
}

/**
 * Merge an Array of Objects into a single Object.
 */
function toObject (arr) {
  var res = {};
  for (var i = 0; i < arr.length; i++) {
    if (arr[i]) {
      extend(res, arr[i]);
    }
  }
  return res
}

/**
 * Perform no operation.
 */
function noop () {}

/**
 * Always return false.
 */
var no = function () { return false; };

/**
 * Return same value
 */
var identity = function (_) { return _; };

/**
 * Generate a static keys string from compiler modules.
 */


/**
 * Check if two values are loosely equal - that is,
 * if they are plain objects, do they have the same shape?
 */
function looseEqual (a, b) {
  var isObjectA = isObject(a);
  var isObjectB = isObject(b);
  if (isObjectA && isObjectB) {
    try {
      return JSON.stringify(a) === JSON.stringify(b)
    } catch (e) {
      // possible circular reference
      return a === b
    }
  } else if (!isObjectA && !isObjectB) {
    return String(a) === String(b)
  } else {
    return false
  }
}

function looseIndexOf (arr, val) {
  for (var i = 0; i < arr.length; i++) {
    if (looseEqual(arr[i], val)) { return i }
  }
  return -1
}

/**
 * Ensure a function is called only once.
 */
function once (fn) {
  var called = false;
  return function () {
    if (!called) {
      called = true;
      fn();
    }
  }
}

/*  */

var config = {
  /**
   * Option merge strategies (used in core/util/options)
   */
  optionMergeStrategies: Object.create(null),

  /**
   * Whether to suppress warnings.
   */
  silent: false,

  /**
   * Show production mode tip message on boot?
   */
  productionTip: process.env.NODE_ENV !== 'production',

  /**
   * Whether to enable devtools
   */
  devtools: process.env.NODE_ENV !== 'production',

  /**
   * Whether to record perf
   */
  performance: false,

  /**
   * Error handler for watcher errors
   */
  errorHandler: null,

  /**
   * Ignore certain custom elements
   */
  ignoredElements: [],

  /**
   * Custom user key aliases for v-on
   */
  keyCodes: Object.create(null),

  /**
   * Check if a tag is reserved so that it cannot be registered as a
   * component. This is platform-dependent and may be overwritten.
   */
  isReservedTag: no,

  /**
   * Check if a tag is an unknown element.
   * Platform-dependent.
   */
  isUnknownElement: no,

  /**
   * Get the namespace of an element
   */
  getTagNamespace: noop,

  /**
   * Parse the real tag name for the specific platform.
   */
  parsePlatformTagName: identity,

  /**
   * Check if an attribute must be bound using property, e.g. value
   * Platform-dependent.
   */
  mustUseProp: no,

  /**
   * List of asset types that a component can own.
   */
  _assetTypes: [
    'component',
    'directive',
    'filter'
  ],

  /**
   * List of lifecycle hooks.
   */
  _lifecycleHooks: [
    'beforeCreate',
    'created',
    'beforeMount',
    'mounted',
    'beforeUpdate',
    'updated',
    'beforeDestroy',
    'destroyed',
    'activated',
    'deactivated'
  ],

  /**
   * Max circular updates allowed in a scheduler flush cycle.
   */
  _maxUpdateCount: 100
};

/*  */

var emptyObject = Object.freeze({});

/**
 * Check if a string starts with $ or _
 */
function isReserved (str) {
  var c = (str + '').charCodeAt(0);
  return c === 0x24 || c === 0x5F
}

/**
 * Define a property.
 */
function def (obj, key, val, enumerable) {
  Object.defineProperty(obj, key, {
    value: val,
    enumerable: !!enumerable,
    writable: true,
    configurable: true
  });
}

/**
 * Parse simple path.
 */
var bailRE = /[^\w.$]/;
function parsePath (path) {
  if (bailRE.test(path)) {
    return
  }
  var segments = path.split('.');
  return function (obj) {
    for (var i = 0; i < segments.length; i++) {
      if (!obj) { return }
      obj = obj[segments[i]];
    }
    return obj
  }
}

/*  */
/* globals MutationObserver */

// can we use __proto__?
var hasProto = '__proto__' in {};

// Browser environment sniffing
var inBrowser = typeof window !== 'undefined';
var UA = inBrowser && window.navigator.userAgent.toLowerCase();
var isIE = UA && /msie|trident/.test(UA);
var isIE9 = UA && UA.indexOf('msie 9.0') > 0;
var isEdge = UA && UA.indexOf('edge/') > 0;
var isAndroid = UA && UA.indexOf('android') > 0;
var isIOS = UA && /iphone|ipad|ipod|ios/.test(UA);
var isChrome = UA && /chrome\/\d+/.test(UA) && !isEdge;

// this needs to be lazy-evaled because vue may be required before
// vue-server-renderer can set VUE_ENV
var _isServer;
var isServerRendering = function () {
  if (_isServer === undefined) {
    /* istanbul ignore if */
    if (!inBrowser && typeof global !== 'undefined') {
      // detect presence of vue-server-renderer and avoid
      // Webpack shimming the process
      _isServer = global['process'].env.VUE_ENV === 'server';
    } else {
      _isServer = false;
    }
  }
  return _isServer
};

// detect devtools
var devtools = inBrowser && window.__VUE_DEVTOOLS_GLOBAL_HOOK__;

/* istanbul ignore next */
function isNative (Ctor) {
  return /native code/.test(Ctor.toString())
}

var hasSymbol =
  typeof Symbol !== 'undefined' && isNative(Symbol) &&
  typeof Reflect !== 'undefined' && isNative(Reflect.ownKeys);

/**
 * Defer a task to execute it asynchronously.
 */
var nextTick = (function () {
  var callbacks = [];
  var pending = false;
  var timerFunc;

  function nextTickHandler () {
    pending = false;
    var copies = callbacks.slice(0);
    callbacks.length = 0;
    for (var i = 0; i < copies.length; i++) {
      copies[i]();
    }
  }

  // the nextTick behavior leverages the microtask queue, which can be accessed
  // via either native Promise.then or MutationObserver.
  // MutationObserver has wider support, however it is seriously bugged in
  // UIWebView in iOS >= 9.3.3 when triggered in touch event handlers. It
  // completely stops working after triggering a few times... so, if native
  // Promise is available, we will use it:
  /* istanbul ignore if */
  if (typeof Promise !== 'undefined' && isNative(Promise)) {
    var p = Promise.resolve();
    var logError = function (err) { console.error(err); };
    timerFunc = function () {
      p.then(nextTickHandler).catch(logError);
      // in problematic UIWebViews, Promise.then doesn't completely break, but
      // it can get stuck in a weird state where callbacks are pushed into the
      // microtask queue but the queue isn't being flushed, until the browser
      // needs to do some other work, e.g. handle a timer. Therefore we can
      // "force" the microtask queue to be flushed by adding an empty timer.
      if (isIOS) { setTimeout(noop); }
    };
  } else if (typeof MutationObserver !== 'undefined' && (
    isNative(MutationObserver) ||
    // PhantomJS and iOS 7.x
    MutationObserver.toString() === '[object MutationObserverConstructor]'
  )) {
    // use MutationObserver where native Promise is not available,
    // e.g. PhantomJS IE11, iOS7, Android 4.4
    var counter = 1;
    var observer = new MutationObserver(nextTickHandler);
    var textNode = document.createTextNode(String(counter));
    observer.observe(textNode, {
      characterData: true
    });
    timerFunc = function () {
      counter = (counter + 1) % 2;
      textNode.data = String(counter);
    };
  } else {
    // fallback to setTimeout
    /* istanbul ignore next */
    timerFunc = function () {
      setTimeout(nextTickHandler, 0);
    };
  }

  return function queueNextTick (cb, ctx) {
    var _resolve;
    callbacks.push(function () {
      if (cb) { cb.call(ctx); }
      if (_resolve) { _resolve(ctx); }
    });
    if (!pending) {
      pending = true;
      timerFunc();
    }
    if (!cb && typeof Promise !== 'undefined') {
      return new Promise(function (resolve) {
        _resolve = resolve;
      })
    }
  }
})();

var _Set;
/* istanbul ignore if */
if (typeof Set !== 'undefined' && isNative(Set)) {
  // use native Set when available.
  _Set = Set;
} else {
  // a non-standard Set polyfill that only works with primitive keys.
  _Set = (function () {
    function Set () {
      this.set = Object.create(null);
    }
    Set.prototype.has = function has (key) {
      return this.set[key] === true
    };
    Set.prototype.add = function add (key) {
      this.set[key] = true;
    };
    Set.prototype.clear = function clear () {
      this.set = Object.create(null);
    };

    return Set;
  }());
}

var warn = noop;
var tip = noop;
var formatComponentName;

if (process.env.NODE_ENV !== 'production') {
  var hasConsole = typeof console !== 'undefined';
  var classifyRE = /(?:^|[-_])(\w)/g;
  var classify = function (str) { return str
    .replace(classifyRE, function (c) { return c.toUpperCase(); })
    .replace(/[-_]/g, ''); };

  warn = function (msg, vm) {
    if (hasConsole && (!config.silent)) {
      console.error("[Vue warn]: " + msg + " " + (
        vm ? formatLocation(formatComponentName(vm)) : ''
      ));
    }
  };

  tip = function (msg, vm) {
    if (hasConsole && (!config.silent)) {
      console.warn("[Vue tip]: " + msg + " " + (
        vm ? formatLocation(formatComponentName(vm)) : ''
      ));
    }
  };

  formatComponentName = function (vm, includeFile) {
    if (vm.$root === vm) {
      return '<Root>'
    }
    var name = typeof vm === 'string'
      ? vm
      : typeof vm === 'function' && vm.options
        ? vm.options.name
        : vm._isVue
          ? vm.$options.name || vm.$options._componentTag
          : vm.name;

    var file = vm._isVue && vm.$options.__file;
    if (!name && file) {
      var match = file.match(/([^/\\]+)\.vue$/);
      name = match && match[1];
    }

    return (
      (name ? ("<" + (classify(name)) + ">") : "<Anonymous>") +
      (file && includeFile !== false ? (" at " + file) : '')
    )
  };

  var formatLocation = function (str) {
    if (str === "<Anonymous>") {
      str += " - use the \"name\" option for better debugging messages.";
    }
    return ("\n(found in " + str + ")")
  };
}

/*  */


var uid$1 = 0;

/**
 * A dep is an observable that can have multiple
 * directives subscribing to it.
 */
var Dep = function Dep () {
  this.id = uid$1++;
  this.subs = [];
};

Dep.prototype.addSub = function addSub (sub) {
  this.subs.push(sub);
};

Dep.prototype.removeSub = function removeSub (sub) {
  remove(this.subs, sub);
};

Dep.prototype.depend = function depend () {
  if (Dep.target) {
    Dep.target.addDep(this);
  }
};

Dep.prototype.notify = function notify () {
  // stabilize the subscriber list first
  var subs = this.subs.slice();
  for (var i = 0, l = subs.length; i < l; i++) {
    subs[i].update();
  }
};

// the current target watcher being evaluated.
// this is globally unique because there could be only one
// watcher being evaluated at any time.
Dep.target = null;
var targetStack = [];

function pushTarget (_target) {
  if (Dep.target) { targetStack.push(Dep.target); }
  Dep.target = _target;
}

function popTarget () {
  Dep.target = targetStack.pop();
}

/*
 * not type checking this file because flow doesn't play well with
 * dynamically accessing methods on Array prototype
 */

var arrayProto = Array.prototype;
var arrayMethods = Object.create(arrayProto);[
  'push',
  'pop',
  'shift',
  'unshift',
  'splice',
  'sort',
  'reverse'
]
.forEach(function (method) {
  // cache original method
  var original = arrayProto[method];
  def(arrayMethods, method, function mutator () {
    var arguments$1 = arguments;

    // avoid leaking arguments:
    // http://jsperf.com/closure-with-arguments
    var i = arguments.length;
    var args = new Array(i);
    while (i--) {
      args[i] = arguments$1[i];
    }
    var result = original.apply(this, args);
    var ob = this.__ob__;
    var inserted;
    switch (method) {
      case 'push':
        inserted = args;
        break
      case 'unshift':
        inserted = args;
        break
      case 'splice':
        inserted = args.slice(2);
        break
    }
    if (inserted) { ob.observeArray(inserted); }
    // notify change
    ob.dep.notify();
    return result
  });
});

/*  */

var arrayKeys = Object.getOwnPropertyNames(arrayMethods);

/**
 * By default, when a reactive property is set, the new value is
 * also converted to become reactive. However when passing down props,
 * we don't want to force conversion because the value may be a nested value
 * under a frozen data structure. Converting it would defeat the optimization.
 */
var observerState = {
  shouldConvert: true,
  isSettingProps: false
};

/**
 * Observer class that are attached to each observed
 * object. Once attached, the observer converts target
 * object's property keys into getter/setters that
 * collect dependencies and dispatches updates.
 */
var Observer = function Observer (value) {
  this.value = value;
  this.dep = new Dep();
  this.vmCount = 0;
  def(value, '__ob__', this);
  if (Array.isArray(value)) {
    var augment = hasProto
      ? protoAugment
      : copyAugment;
    augment(value, arrayMethods, arrayKeys);
    this.observeArray(value);
  } else {
    this.walk(value);
  }
};

/**
 * Walk through each property and convert them into
 * getter/setters. This method should only be called when
 * value type is Object.
 */
Observer.prototype.walk = function walk (obj) {
  var keys = Object.keys(obj);
  for (var i = 0; i < keys.length; i++) {
    defineReactive$$1(obj, keys[i], obj[keys[i]]);
  }
};

/**
 * Observe a list of Array items.
 */
Observer.prototype.observeArray = function observeArray (items) {
  for (var i = 0, l = items.length; i < l; i++) {
    observe(items[i]);
  }
};

// helpers

/**
 * Augment an target Object or Array by intercepting
 * the prototype chain using __proto__
 */
function protoAugment (target, src) {
  /* eslint-disable no-proto */
  target.__proto__ = src;
  /* eslint-enable no-proto */
}

/**
 * Augment an target Object or Array by defining
 * hidden properties.
 */
/* istanbul ignore next */
function copyAugment (target, src, keys) {
  for (var i = 0, l = keys.length; i < l; i++) {
    var key = keys[i];
    def(target, key, src[key]);
  }
}

/**
 * Attempt to create an observer instance for a value,
 * returns the new observer if successfully observed,
 * or the existing observer if the value already has one.
 */
function observe (value, asRootData) {
  if (!isObject(value)) {
    return
  }
  var ob;
  if (hasOwn(value, '__ob__') && value.__ob__ instanceof Observer) {
    ob = value.__ob__;
  } else if (
    observerState.shouldConvert &&
    !isServerRendering() &&
    (Array.isArray(value) || isPlainObject(value)) &&
    Object.isExtensible(value) &&
    !value._isVue
  ) {
    ob = new Observer(value);
  }
  if (asRootData && ob) {
    ob.vmCount++;
  }
  return ob
}

/**
 * Define a reactive property on an Object.
 */
function defineReactive$$1 (
  obj,
  key,
  val,
  customSetter
) {
  var dep = new Dep();

  var property = Object.getOwnPropertyDescriptor(obj, key);
  if (property && property.configurable === false) {
    return
  }

  // cater for pre-defined getter/setters
  var getter = property && property.get;
  var setter = property && property.set;

  var childOb = observe(val);
  Object.defineProperty(obj, key, {
    enumerable: true,
    configurable: true,
    get: function reactiveGetter () {
      var value = getter ? getter.call(obj) : val;
      if (Dep.target) {
        dep.depend();
        if (childOb) {
          childOb.dep.depend();
        }
        if (Array.isArray(value)) {
          dependArray(value);
        }
      }
      return value
    },
    set: function reactiveSetter (newVal) {
      var value = getter ? getter.call(obj) : val;
      /* eslint-disable no-self-compare */
      if (newVal === value || (newVal !== newVal && value !== value)) {
        return
      }
      /* eslint-enable no-self-compare */
      if (process.env.NODE_ENV !== 'production' && customSetter) {
        customSetter();
      }
      if (setter) {
        setter.call(obj, newVal);
      } else {
        val = newVal;
      }
      childOb = observe(newVal);
      dep.notify();
    }
  });
}

/**
 * Set a property on an object. Adds the new property and
 * triggers change notification if the property doesn't
 * already exist.
 */
function set (target, key, val) {
  if (Array.isArray(target) && typeof key === 'number') {
    target.length = Math.max(target.length, key);
    target.splice(key, 1, val);
    return val
  }
  if (hasOwn(target, key)) {
    target[key] = val;
    return val
  }
  var ob = (target ).__ob__;
  if (target._isVue || (ob && ob.vmCount)) {
    process.env.NODE_ENV !== 'production' && warn(
      'Avoid adding reactive properties to a Vue instance or its root $data ' +
      'at runtime - declare it upfront in the data option.'
    );
    return val
  }
  if (!ob) {
    target[key] = val;
    return val
  }
  defineReactive$$1(ob.value, key, val);
  ob.dep.notify();
  return val
}

/**
 * Delete a property and trigger change if necessary.
 */
function del (target, key) {
  if (Array.isArray(target) && typeof key === 'number') {
    target.splice(key, 1);
    return
  }
  var ob = (target ).__ob__;
  if (target._isVue || (ob && ob.vmCount)) {
    process.env.NODE_ENV !== 'production' && warn(
      'Avoid deleting properties on a Vue instance or its root $data ' +
      '- just set it to null.'
    );
    return
  }
  if (!hasOwn(target, key)) {
    return
  }
  delete target[key];
  if (!ob) {
    return
  }
  ob.dep.notify();
}

/**
 * Collect dependencies on array elements when the array is touched, since
 * we cannot intercept array element access like property getters.
 */
function dependArray (value) {
  for (var e = (void 0), i = 0, l = value.length; i < l; i++) {
    e = value[i];
    e && e.__ob__ && e.__ob__.dep.depend();
    if (Array.isArray(e)) {
      dependArray(e);
    }
  }
}

/*  */

/**
 * Option overwriting strategies are functions that handle
 * how to merge a parent option value and a child option
 * value into the final value.
 */
var strats = config.optionMergeStrategies;

/**
 * Options with restrictions
 */
if (process.env.NODE_ENV !== 'production') {
  strats.el = strats.propsData = function (parent, child, vm, key) {
    if (!vm) {
      warn(
        "option \"" + key + "\" can only be used during instance " +
        'creation with the `new` keyword.'
      );
    }
    return defaultStrat(parent, child)
  };
}

/**
 * Helper that recursively merges two data objects together.
 */
function mergeData (to, from) {
  if (!from) { return to }
  var key, toVal, fromVal;
  var keys = Object.keys(from);
  for (var i = 0; i < keys.length; i++) {
    key = keys[i];
    toVal = to[key];
    fromVal = from[key];
    if (!hasOwn(to, key)) {
      set(to, key, fromVal);
    } else if (isPlainObject(toVal) && isPlainObject(fromVal)) {
      mergeData(toVal, fromVal);
    }
  }
  return to
}

/**
 * Data
 */
strats.data = function (
  parentVal,
  childVal,
  vm
) {
  if (!vm) {
    // in a Vue.extend merge, both should be functions
    if (!childVal) {
      return parentVal
    }
    if (typeof childVal !== 'function') {
      process.env.NODE_ENV !== 'production' && warn(
        'The "data" option should be a function ' +
        'that returns a per-instance value in component ' +
        'definitions.',
        vm
      );
      return parentVal
    }
    if (!parentVal) {
      return childVal
    }
    // when parentVal & childVal are both present,
    // we need to return a function that returns the
    // merged result of both functions... no need to
    // check if parentVal is a function here because
    // it has to be a function to pass previous merges.
    return function mergedDataFn () {
      return mergeData(
        childVal.call(this),
        parentVal.call(this)
      )
    }
  } else if (parentVal || childVal) {
    return function mergedInstanceDataFn () {
      // instance merge
      var instanceData = typeof childVal === 'function'
        ? childVal.call(vm)
        : childVal;
      var defaultData = typeof parentVal === 'function'
        ? parentVal.call(vm)
        : undefined;
      if (instanceData) {
        return mergeData(instanceData, defaultData)
      } else {
        return defaultData
      }
    }
  }
};

/**
 * Hooks and props are merged as arrays.
 */
function mergeHook (
  parentVal,
  childVal
) {
  return childVal
    ? parentVal
      ? parentVal.concat(childVal)
      : Array.isArray(childVal)
        ? childVal
        : [childVal]
    : parentVal
}

config._lifecycleHooks.forEach(function (hook) {
  strats[hook] = mergeHook;
});

/**
 * Assets
 *
 * When a vm is present (instance creation), we need to do
 * a three-way merge between constructor options, instance
 * options and parent options.
 */
function mergeAssets (parentVal, childVal) {
  var res = Object.create(parentVal || null);
  return childVal
    ? extend(res, childVal)
    : res
}

config._assetTypes.forEach(function (type) {
  strats[type + 's'] = mergeAssets;
});

/**
 * Watchers.
 *
 * Watchers hashes should not overwrite one
 * another, so we merge them as arrays.
 */
strats.watch = function (parentVal, childVal) {
  /* istanbul ignore if */
  if (!childVal) { return Object.create(parentVal || null) }
  if (!parentVal) { return childVal }
  var ret = {};
  extend(ret, parentVal);
  for (var key in childVal) {
    var parent = ret[key];
    var child = childVal[key];
    if (parent && !Array.isArray(parent)) {
      parent = [parent];
    }
    ret[key] = parent
      ? parent.concat(child)
      : [child];
  }
  return ret
};

/**
 * Other object hashes.
 */
strats.props =
strats.methods =
strats.computed = function (parentVal, childVal) {
  if (!childVal) { return Object.create(parentVal || null) }
  if (!parentVal) { return childVal }
  var ret = Object.create(null);
  extend(ret, parentVal);
  extend(ret, childVal);
  return ret
};

/**
 * Default strategy.
 */
var defaultStrat = function (parentVal, childVal) {
  return childVal === undefined
    ? parentVal
    : childVal
};

/**
 * Validate component names
 */
function checkComponents (options) {
  for (var key in options.components) {
    var lower = key.toLowerCase();
    if (isBuiltInTag(lower) || config.isReservedTag(lower)) {
      warn(
        'Do not use built-in or reserved HTML elements as component ' +
        'id: ' + key
      );
    }
  }
}

/**
 * Ensure all props option syntax are normalized into the
 * Object-based format.
 */
function normalizeProps (options) {
  var props = options.props;
  if (!props) { return }
  var res = {};
  var i, val, name;
  if (Array.isArray(props)) {
    i = props.length;
    while (i--) {
      val = props[i];
      if (typeof val === 'string') {
        name = camelize(val);
        res[name] = { type: null };
      } else if (process.env.NODE_ENV !== 'production') {
        warn('props must be strings when using array syntax.');
      }
    }
  } else if (isPlainObject(props)) {
    for (var key in props) {
      val = props[key];
      name = camelize(key);
      res[name] = isPlainObject(val)
        ? val
        : { type: val };
    }
  }
  options.props = res;
}

/**
 * Normalize raw function directives into object format.
 */
function normalizeDirectives (options) {
  var dirs = options.directives;
  if (dirs) {
    for (var key in dirs) {
      var def = dirs[key];
      if (typeof def === 'function') {
        dirs[key] = { bind: def, update: def };
      }
    }
  }
}

/**
 * Merge two option objects into a new one.
 * Core utility used in both instantiation and inheritance.
 */
function mergeOptions (
  parent,
  child,
  vm
) {
  if (process.env.NODE_ENV !== 'production') {
    checkComponents(child);
  }
  normalizeProps(child);
  normalizeDirectives(child);
  var extendsFrom = child.extends;
  if (extendsFrom) {
    parent = typeof extendsFrom === 'function'
      ? mergeOptions(parent, extendsFrom.options, vm)
      : mergeOptions(parent, extendsFrom, vm);
  }
  if (child.mixins) {
    for (var i = 0, l = child.mixins.length; i < l; i++) {
      var mixin = child.mixins[i];
      if (mixin.prototype instanceof Vue$2) {
        mixin = mixin.options;
      }
      parent = mergeOptions(parent, mixin, vm);
    }
  }
  var options = {};
  var key;
  for (key in parent) {
    mergeField(key);
  }
  for (key in child) {
    if (!hasOwn(parent, key)) {
      mergeField(key);
    }
  }
  function mergeField (key) {
    var strat = strats[key] || defaultStrat;
    options[key] = strat(parent[key], child[key], vm, key);
  }
  return options
}

/**
 * Resolve an asset.
 * This function is used because child instances need access
 * to assets defined in its ancestor chain.
 */
function resolveAsset (
  options,
  type,
  id,
  warnMissing
) {
  /* istanbul ignore if */
  if (typeof id !== 'string') {
    return
  }
  var assets = options[type];
  // check local registration variations first
  if (hasOwn(assets, id)) { return assets[id] }
  var camelizedId = camelize(id);
  if (hasOwn(assets, camelizedId)) { return assets[camelizedId] }
  var PascalCaseId = capitalize(camelizedId);
  if (hasOwn(assets, PascalCaseId)) { return assets[PascalCaseId] }
  // fallback to prototype chain
  var res = assets[id] || assets[camelizedId] || assets[PascalCaseId];
  if (process.env.NODE_ENV !== 'production' && warnMissing && !res) {
    warn(
      'Failed to resolve ' + type.slice(0, -1) + ': ' + id,
      options
    );
  }
  return res
}

/*  */

function validateProp (
  key,
  propOptions,
  propsData,
  vm
) {
  var prop = propOptions[key];
  var absent = !hasOwn(propsData, key);
  var value = propsData[key];
  // handle boolean props
  if (isType(Boolean, prop.type)) {
    if (absent && !hasOwn(prop, 'default')) {
      value = false;
    } else if (!isType(String, prop.type) && (value === '' || value === hyphenate(key))) {
      value = true;
    }
  }
  // check default value
  if (value === undefined) {
    value = getPropDefaultValue(vm, prop, key);
    // since the default value is a fresh copy,
    // make sure to observe it.
    var prevShouldConvert = observerState.shouldConvert;
    observerState.shouldConvert = true;
    observe(value);
    observerState.shouldConvert = prevShouldConvert;
  }
  if (process.env.NODE_ENV !== 'production') {
    assertProp(prop, key, value, vm, absent);
  }
  return value
}

/**
 * Get the default value of a prop.
 */
function getPropDefaultValue (vm, prop, key) {
  // no default, return undefined
  if (!hasOwn(prop, 'default')) {
    return undefined
  }
  var def = prop.default;
  // warn against non-factory defaults for Object & Array
  if (process.env.NODE_ENV !== 'production' && isObject(def)) {
    warn(
      'Invalid default value for prop "' + key + '": ' +
      'Props with type Object/Array must use a factory function ' +
      'to return the default value.',
      vm
    );
  }
  // the raw prop value was also undefined from previous render,
  // return previous default value to avoid unnecessary watcher trigger
  if (vm && vm.$options.propsData &&
    vm.$options.propsData[key] === undefined &&
    vm._props[key] !== undefined) {
    return vm._props[key]
  }
  // call factory function for non-Function types
  // a value is Function if its prototype is function even across different execution context
  return typeof def === 'function' && getType(prop.type) !== 'Function'
    ? def.call(vm)
    : def
}

/**
 * Assert whether a prop is valid.
 */
function assertProp (
  prop,
  name,
  value,
  vm,
  absent
) {
  if (prop.required && absent) {
    warn(
      'Missing required prop: "' + name + '"',
      vm
    );
    return
  }
  if (value == null && !prop.required) {
    return
  }
  var type = prop.type;
  var valid = !type || type === true;
  var expectedTypes = [];
  if (type) {
    if (!Array.isArray(type)) {
      type = [type];
    }
    for (var i = 0; i < type.length && !valid; i++) {
      var assertedType = assertType(value, type[i]);
      expectedTypes.push(assertedType.expectedType || '');
      valid = assertedType.valid;
    }
  }
  if (!valid) {
    warn(
      'Invalid prop: type check failed for prop "' + name + '".' +
      ' Expected ' + expectedTypes.map(capitalize).join(', ') +
      ', got ' + Object.prototype.toString.call(value).slice(8, -1) + '.',
      vm
    );
    return
  }
  var validator = prop.validator;
  if (validator) {
    if (!validator(value)) {
      warn(
        'Invalid prop: custom validator check failed for prop "' + name + '".',
        vm
      );
    }
  }
}

/**
 * Assert the type of a value
 */
function assertType (value, type) {
  var valid;
  var expectedType = getType(type);
  if (expectedType === 'String') {
    valid = typeof value === (expectedType = 'string');
  } else if (expectedType === 'Number') {
    valid = typeof value === (expectedType = 'number');
  } else if (expectedType === 'Boolean') {
    valid = typeof value === (expectedType = 'boolean');
  } else if (expectedType === 'Function') {
    valid = typeof value === (expectedType = 'function');
  } else if (expectedType === 'Object') {
    valid = isPlainObject(value);
  } else if (expectedType === 'Array') {
    valid = Array.isArray(value);
  } else {
    valid = value instanceof type;
  }
  return {
    valid: valid,
    expectedType: expectedType
  }
}

/**
 * Use function string name to check built-in types,
 * because a simple equality check will fail when running
 * across different vms / iframes.
 */
function getType (fn) {
  var match = fn && fn.toString().match(/^\s*function (\w+)/);
  return match && match[1]
}

function isType (type, fn) {
  if (!Array.isArray(fn)) {
    return getType(fn) === getType(type)
  }
  for (var i = 0, len = fn.length; i < len; i++) {
    if (getType(fn[i]) === getType(type)) {
      return true
    }
  }
  /* istanbul ignore next */
  return false
}

function handleError (err, vm, info) {
  if (config.errorHandler) {
    config.errorHandler.call(null, err, vm, info);
  } else {
    if (process.env.NODE_ENV !== 'production') {
      warn(("Error in " + info + ":"), vm);
    }
    /* istanbul ignore else */
    if (inBrowser && typeof console !== 'undefined') {
      console.error(err);
    } else {
      throw err
    }
  }
}

/* not type checking this file because flow doesn't play well with Proxy */

var initProxy;

if (process.env.NODE_ENV !== 'production') {
  var allowedGlobals = makeMap(
    'Infinity,undefined,NaN,isFinite,isNaN,' +
    'parseFloat,parseInt,decodeURI,decodeURIComponent,encodeURI,encodeURIComponent,' +
    'Math,Number,Date,Array,Object,Boolean,String,RegExp,Map,Set,JSON,Intl,' +
    'require' // for Webpack/Browserify
  );

  var warnNonPresent = function (target, key) {
    warn(
      "Property or method \"" + key + "\" is not defined on the instance but " +
      "referenced during render. Make sure to declare reactive data " +
      "properties in the data option.",
      target
    );
  };

  var hasProxy =
    typeof Proxy !== 'undefined' &&
    Proxy.toString().match(/native code/);

  if (hasProxy) {
    var isBuiltInModifier = makeMap('stop,prevent,self,ctrl,shift,alt,meta');
    config.keyCodes = new Proxy(config.keyCodes, {
      set: function set (target, key, value) {
        if (isBuiltInModifier(key)) {
          warn(("Avoid overwriting built-in modifier in config.keyCodes: ." + key));
          return false
        } else {
          target[key] = value;
          return true
        }
      }
    });
  }

  var hasHandler = {
    has: function has (target, key) {
      var has = key in target;
      var isAllowed = allowedGlobals(key) || key.charAt(0) === '_';
      if (!has && !isAllowed) {
        warnNonPresent(target, key);
      }
      return has || !isAllowed
    }
  };

  var getHandler = {
    get: function get (target, key) {
      if (typeof key === 'string' && !(key in target)) {
        warnNonPresent(target, key);
      }
      return target[key]
    }
  };

  initProxy = function initProxy (vm) {
    if (hasProxy) {
      // determine which proxy handler to use
      var options = vm.$options;
      var handlers = options.render && options.render._withStripped
        ? getHandler
        : hasHandler;
      vm._renderProxy = new Proxy(vm, handlers);
    } else {
      vm._renderProxy = vm;
    }
  };
}

var mark;
var measure;

if (process.env.NODE_ENV !== 'production') {
  var perf = inBrowser && window.performance;
  /* istanbul ignore if */
  if (
    perf &&
    perf.mark &&
    perf.measure &&
    perf.clearMarks &&
    perf.clearMeasures
  ) {
    mark = function (tag) { return perf.mark(tag); };
    measure = function (name, startTag, endTag) {
      perf.measure(name, startTag, endTag);
      perf.clearMarks(startTag);
      perf.clearMarks(endTag);
      perf.clearMeasures(name);
    };
  }
}

/*  */

var VNode = function VNode (
  tag,
  data,
  children,
  text,
  elm,
  context,
  componentOptions
) {
  this.tag = tag;
  this.data = data;
  this.children = children;
  this.text = text;
  this.elm = elm;
  this.ns = undefined;
  this.context = context;
  this.functionalContext = undefined;
  this.key = data && data.key;
  this.componentOptions = componentOptions;
  this.componentInstance = undefined;
  this.parent = undefined;
  this.raw = false;
  this.isStatic = false;
  this.isRootInsert = true;
  this.isComment = false;
  this.isCloned = false;
  this.isOnce = false;
};

var prototypeAccessors = { child: {} };

// DEPRECATED: alias for componentInstance for backwards compat.
/* istanbul ignore next */
prototypeAccessors.child.get = function () {
  return this.componentInstance
};

Object.defineProperties( VNode.prototype, prototypeAccessors );

var createEmptyVNode = function () {
  var node = new VNode();
  node.text = '';
  node.isComment = true;
  return node
};

function createTextVNode (val) {
  return new VNode(undefined, undefined, undefined, String(val))
}

// optimized shallow clone
// used for static nodes and slot nodes because they may be reused across
// multiple renders, cloning them avoids errors when DOM manipulations rely
// on their elm reference.
function cloneVNode (vnode) {
  var cloned = new VNode(
    vnode.tag,
    vnode.data,
    vnode.children,
    vnode.text,
    vnode.elm,
    vnode.context,
    vnode.componentOptions
  );
  cloned.ns = vnode.ns;
  cloned.isStatic = vnode.isStatic;
  cloned.key = vnode.key;
  cloned.isCloned = true;
  return cloned
}

function cloneVNodes (vnodes) {
  var len = vnodes.length;
  var res = new Array(len);
  for (var i = 0; i < len; i++) {
    res[i] = cloneVNode(vnodes[i]);
  }
  return res
}

/*  */

var normalizeEvent = cached(function (name) {
  var once$$1 = name.charAt(0) === '~'; // Prefixed last, checked first
  name = once$$1 ? name.slice(1) : name;
  var capture = name.charAt(0) === '!';
  name = capture ? name.slice(1) : name;
  return {
    name: name,
    once: once$$1,
    capture: capture
  }
});

function createFnInvoker (fns) {
  function invoker () {
    var arguments$1 = arguments;

    var fns = invoker.fns;
    if (Array.isArray(fns)) {
      for (var i = 0; i < fns.length; i++) {
        fns[i].apply(null, arguments$1);
      }
    } else {
      // return handler return value for single handlers
      return fns.apply(null, arguments)
    }
  }
  invoker.fns = fns;
  return invoker
}

function updateListeners (
  on,
  oldOn,
  add,
  remove$$1,
  vm
) {
  var name, cur, old, event;
  for (name in on) {
    cur = on[name];
    old = oldOn[name];
    event = normalizeEvent(name);
    if (!cur) {
      process.env.NODE_ENV !== 'production' && warn(
        "Invalid handler for event \"" + (event.name) + "\": got " + String(cur),
        vm
      );
    } else if (!old) {
      if (!cur.fns) {
        cur = on[name] = createFnInvoker(cur);
      }
      add(event.name, cur, event.once, event.capture);
    } else if (cur !== old) {
      old.fns = cur;
      on[name] = old;
    }
  }
  for (name in oldOn) {
    if (!on[name]) {
      event = normalizeEvent(name);
      remove$$1(event.name, oldOn[name], event.capture);
    }
  }
}

/*  */

function mergeVNodeHook (def, hookKey, hook) {
  var invoker;
  var oldHook = def[hookKey];

  function wrappedHook () {
    hook.apply(this, arguments);
    // important: remove merged hook to ensure it's called only once
    // and prevent memory leak
    remove(invoker.fns, wrappedHook);
  }

  if (!oldHook) {
    // no existing hook
    invoker = createFnInvoker([wrappedHook]);
  } else {
    /* istanbul ignore if */
    if (oldHook.fns && oldHook.merged) {
      // already a merged invoker
      invoker = oldHook;
      invoker.fns.push(wrappedHook);
    } else {
      // existing plain hook
      invoker = createFnInvoker([oldHook, wrappedHook]);
    }
  }

  invoker.merged = true;
  def[hookKey] = invoker;
}

/*  */

// The template compiler attempts to minimize the need for normalization by
// statically analyzing the template at compile time.
//
// For plain HTML markup, normalization can be completely skipped because the
// generated render function is guaranteed to return Array<VNode>. There are
// two cases where extra normalization is needed:

// 1. When the children contains components - because a functional component
// may return an Array instead of a single root. In this case, just a simple
// normalization is needed - if any child is an Array, we flatten the whole
// thing with Array.prototype.concat. It is guaranteed to be only 1-level deep
// because functional components already normalize their own children.
function simpleNormalizeChildren (children) {
  for (var i = 0; i < children.length; i++) {
    if (Array.isArray(children[i])) {
      return Array.prototype.concat.apply([], children)
    }
  }
  return children
}

// 2. When the children contains constructs that always generated nested Arrays,
// e.g. <template>, <slot>, v-for, or when the children is provided by user
// with hand-written render functions / JSX. In such cases a full normalization
// is needed to cater to all possible types of children values.
function normalizeChildren (children) {
  return isPrimitive(children)
    ? [createTextVNode(children)]
    : Array.isArray(children)
      ? normalizeArrayChildren(children)
      : undefined
}

function normalizeArrayChildren (children, nestedIndex) {
  var res = [];
  var i, c, last;
  for (i = 0; i < children.length; i++) {
    c = children[i];
    if (c == null || typeof c === 'boolean') { continue }
    last = res[res.length - 1];
    //  nested
    if (Array.isArray(c)) {
      res.push.apply(res, normalizeArrayChildren(c, ((nestedIndex || '') + "_" + i)));
    } else if (isPrimitive(c)) {
      if (last && last.text) {
        last.text += String(c);
      } else if (c !== '') {
        // convert primitive to vnode
        res.push(createTextVNode(c));
      }
    } else {
      if (c.text && last && last.text) {
        res[res.length - 1] = createTextVNode(last.text + c.text);
      } else {
        // default key for nested array children (likely generated by v-for)
        if (c.tag && c.key == null && nestedIndex != null) {
          c.key = "__vlist" + nestedIndex + "_" + i + "__";
        }
        res.push(c);
      }
    }
  }
  return res
}

/*  */

function getFirstComponentChild (children) {
  return children && children.filter(function (c) { return c && c.componentOptions; })[0]
}

/*  */

function initEvents (vm) {
  vm._events = Object.create(null);
  vm._hasHookEvent = false;
  // init parent attached events
  var listeners = vm.$options._parentListeners;
  if (listeners) {
    updateComponentListeners(vm, listeners);
  }
}

var target;

function add (event, fn, once$$1) {
  if (once$$1) {
    target.$once(event, fn);
  } else {
    target.$on(event, fn);
  }
}

function remove$1 (event, fn) {
  target.$off(event, fn);
}

function updateComponentListeners (
  vm,
  listeners,
  oldListeners
) {
  target = vm;
  updateListeners(listeners, oldListeners || {}, add, remove$1, vm);
}

function eventsMixin (Vue) {
  var hookRE = /^hook:/;
  Vue.prototype.$on = function (event, fn) {
    var this$1 = this;

    var vm = this;
    if (Array.isArray(event)) {
      for (var i = 0, l = event.length; i < l; i++) {
        this$1.$on(event[i], fn);
      }
    } else {
      (vm._events[event] || (vm._events[event] = [])).push(fn);
      // optimize hook:event cost by using a boolean flag marked at registration
      // instead of a hash lookup
      if (hookRE.test(event)) {
        vm._hasHookEvent = true;
      }
    }
    return vm
  };

  Vue.prototype.$once = function (event, fn) {
    var vm = this;
    function on () {
      vm.$off(event, on);
      fn.apply(vm, arguments);
    }
    on.fn = fn;
    vm.$on(event, on);
    return vm
  };

  Vue.prototype.$off = function (event, fn) {
    var this$1 = this;

    var vm = this;
    // all
    if (!arguments.length) {
      vm._events = Object.create(null);
      return vm
    }
    // array of events
    if (Array.isArray(event)) {
      for (var i$1 = 0, l = event.length; i$1 < l; i$1++) {
        this$1.$off(event[i$1], fn);
      }
      return vm
    }
    // specific event
    var cbs = vm._events[event];
    if (!cbs) {
      return vm
    }
    if (arguments.length === 1) {
      vm._events[event] = null;
      return vm
    }
    // specific handler
    var cb;
    var i = cbs.length;
    while (i--) {
      cb = cbs[i];
      if (cb === fn || cb.fn === fn) {
        cbs.splice(i, 1);
        break
      }
    }
    return vm
  };

  Vue.prototype.$emit = function (event) {
    var vm = this;
    if (process.env.NODE_ENV !== 'production') {
      var lowerCaseEvent = event.toLowerCase();
      if (lowerCaseEvent !== event && vm._events[lowerCaseEvent]) {
        tip(
          "Event \"" + lowerCaseEvent + "\" is emitted in component " +
          (formatComponentName(vm)) + " but the handler is registered for \"" + event + "\". " +
          "Note that HTML attributes are case-insensitive and you cannot use " +
          "v-on to listen to camelCase events when using in-DOM templates. " +
          "You should probably use \"" + (hyphenate(event)) + "\" instead of \"" + event + "\"."
        );
      }
    }
    var cbs = vm._events[event];
    if (cbs) {
      cbs = cbs.length > 1 ? toArray(cbs) : cbs;
      var args = toArray(arguments, 1);
      for (var i = 0, l = cbs.length; i < l; i++) {
        cbs[i].apply(vm, args);
      }
    }
    return vm
  };
}

/*  */

/**
 * Runtime helper for resolving raw children VNodes into a slot object.
 */
function resolveSlots (
  children,
  context
) {
  var slots = {};
  if (!children) {
    return slots
  }
  var defaultSlot = [];
  var name, child;
  for (var i = 0, l = children.length; i < l; i++) {
    child = children[i];
    // named slots should only be respected if the vnode was rendered in the
    // same context.
    if ((child.context === context || child.functionalContext === context) &&
        child.data && (name = child.data.slot)) {
      var slot = (slots[name] || (slots[name] = []));
      if (child.tag === 'template') {
        slot.push.apply(slot, child.children);
      } else {
        slot.push(child);
      }
    } else {
      defaultSlot.push(child);
    }
  }
  // ignore whitespace
  if (!defaultSlot.every(isWhitespace)) {
    slots.default = defaultSlot;
  }
  return slots
}

function isWhitespace (node) {
  return node.isComment || node.text === ' '
}

function resolveScopedSlots (
  fns
) {
  var res = {};
  for (var i = 0; i < fns.length; i++) {
    res[fns[i][0]] = fns[i][1];
  }
  return res
}

/*  */

var activeInstance = null;

function initLifecycle (vm) {
  var options = vm.$options;

  // locate first non-abstract parent
  var parent = options.parent;
  if (parent && !options.abstract) {
    while (parent.$options.abstract && parent.$parent) {
      parent = parent.$parent;
    }
    parent.$children.push(vm);
  }

  vm.$parent = parent;
  vm.$root = parent ? parent.$root : vm;

  vm.$children = [];
  vm.$refs = {};

  vm._watcher = null;
  vm._inactive = null;
  vm._directInactive = false;
  vm._isMounted = false;
  vm._isDestroyed = false;
  vm._isBeingDestroyed = false;
}

function lifecycleMixin (Vue) {
  Vue.prototype._update = function (vnode, hydrating) {
    var vm = this;
    if (vm._isMounted) {
      callHook(vm, 'beforeUpdate');
    }
    var prevEl = vm.$el;
    var prevVnode = vm._vnode;
    var prevActiveInstance = activeInstance;
    activeInstance = vm;
    vm._vnode = vnode;
    // Vue.prototype.__patch__ is injected in entry points
    // based on the rendering backend used.
    if (!prevVnode) {
      // initial render
      vm.$el = vm.__patch__(
        vm.$el, vnode, hydrating, false /* removeOnly */,
        vm.$options._parentElm,
        vm.$options._refElm
      );
    } else {
      // updates
      vm.$el = vm.__patch__(prevVnode, vnode);
    }
    activeInstance = prevActiveInstance;
    // update __vue__ reference
    if (prevEl) {
      prevEl.__vue__ = null;
    }
    if (vm.$el) {
      vm.$el.__vue__ = vm;
    }
    // if parent is an HOC, update its $el as well
    if (vm.$vnode && vm.$parent && vm.$vnode === vm.$parent._vnode) {
      vm.$parent.$el = vm.$el;
    }
    // updated hook is called by the scheduler to ensure that children are
    // updated in a parent's updated hook.
  };

  Vue.prototype.$forceUpdate = function () {
    var vm = this;
    if (vm._watcher) {
      vm._watcher.update();
    }
  };

  Vue.prototype.$destroy = function () {
    var vm = this;
    if (vm._isBeingDestroyed) {
      return
    }
    callHook(vm, 'beforeDestroy');
    vm._isBeingDestroyed = true;
    // remove self from parent
    var parent = vm.$parent;
    if (parent && !parent._isBeingDestroyed && !vm.$options.abstract) {
      remove(parent.$children, vm);
    }
    // teardown watchers
    if (vm._watcher) {
      vm._watcher.teardown();
    }
    var i = vm._watchers.length;
    while (i--) {
      vm._watchers[i].teardown();
    }
    // remove reference from data ob
    // frozen object may not have observer.
    if (vm._data.__ob__) {
      vm._data.__ob__.vmCount--;
    }
    // call the last hook...
    vm._isDestroyed = true;
    // invoke destroy hooks on current rendered tree
    vm.__patch__(vm._vnode, null);
    // fire destroyed hook
    callHook(vm, 'destroyed');
    // turn off all instance listeners.
    vm.$off();
    // remove __vue__ reference
    if (vm.$el) {
      vm.$el.__vue__ = null;
    }
    // remove reference to DOM nodes (prevents leak)
    vm.$options._parentElm = vm.$options._refElm = null;
  };
}

function mountComponent (
  vm,
  el,
  hydrating
) {
  vm.$el = el;
  if (!vm.$options.render) {
    vm.$options.render = createEmptyVNode;
    if (process.env.NODE_ENV !== 'production') {
      /* istanbul ignore if */
      if ((vm.$options.template && vm.$options.template.charAt(0) !== '#') ||
        vm.$options.el || el) {
        warn(
          'You are using the runtime-only build of Vue where the template ' +
          'compiler is not available. Either pre-compile the templates into ' +
          'render functions, or use the compiler-included build.',
          vm
        );
      } else {
        warn(
          'Failed to mount component: template or render function not defined.',
          vm
        );
      }
    }
  }
  callHook(vm, 'beforeMount');

  var updateComponent;
  /* istanbul ignore if */
  if (process.env.NODE_ENV !== 'production' && config.performance && mark) {
    updateComponent = function () {
      var name = vm._name;
      var id = vm._uid;
      var startTag = "vue-perf-start:" + id;
      var endTag = "vue-perf-end:" + id;

      mark(startTag);
      var vnode = vm._render();
      mark(endTag);
      measure((name + " render"), startTag, endTag);

      mark(startTag);
      vm._update(vnode, hydrating);
      mark(endTag);
      measure((name + " patch"), startTag, endTag);
    };
  } else {
    updateComponent = function () {
      vm._update(vm._render(), hydrating);
    };
  }

  vm._watcher = new Watcher(vm, updateComponent, noop);
  hydrating = false;

  // manually mounted instance, call mounted on self
  // mounted is called for render-created child components in its inserted hook
  if (vm.$vnode == null) {
    vm._isMounted = true;
    callHook(vm, 'mounted');
  }
  return vm
}

function updateChildComponent (
  vm,
  propsData,
  listeners,
  parentVnode,
  renderChildren
) {
  // determine whether component has slot children
  // we need to do this before overwriting $options._renderChildren
  var hasChildren = !!(
    renderChildren ||               // has new static slots
    vm.$options._renderChildren ||  // has old static slots
    parentVnode.data.scopedSlots || // has new scoped slots
    vm.$scopedSlots !== emptyObject // has old scoped slots
  );

  vm.$options._parentVnode = parentVnode;
  vm.$vnode = parentVnode; // update vm's placeholder node without re-render
  if (vm._vnode) { // update child tree's parent
    vm._vnode.parent = parentVnode;
  }
  vm.$options._renderChildren = renderChildren;

  // update props
  if (propsData && vm.$options.props) {
    observerState.shouldConvert = false;
    if (process.env.NODE_ENV !== 'production') {
      observerState.isSettingProps = true;
    }
    var props = vm._props;
    var propKeys = vm.$options._propKeys || [];
    for (var i = 0; i < propKeys.length; i++) {
      var key = propKeys[i];
      props[key] = validateProp(key, vm.$options.props, propsData, vm);
    }
    observerState.shouldConvert = true;
    if (process.env.NODE_ENV !== 'production') {
      observerState.isSettingProps = false;
    }
    // keep a copy of raw propsData
    vm.$options.propsData = propsData;
  }
  // update listeners
  if (listeners) {
    var oldListeners = vm.$options._parentListeners;
    vm.$options._parentListeners = listeners;
    updateComponentListeners(vm, listeners, oldListeners);
  }
  // resolve slots + force update if has children
  if (hasChildren) {
    vm.$slots = resolveSlots(renderChildren, parentVnode.context);
    vm.$forceUpdate();
  }
}

function isInInactiveTree (vm) {
  while (vm && (vm = vm.$parent)) {
    if (vm._inactive) { return true }
  }
  return false
}

function activateChildComponent (vm, direct) {
  if (direct) {
    vm._directInactive = false;
    if (isInInactiveTree(vm)) {
      return
    }
  } else if (vm._directInactive) {
    return
  }
  if (vm._inactive || vm._inactive == null) {
    vm._inactive = false;
    for (var i = 0; i < vm.$children.length; i++) {
      activateChildComponent(vm.$children[i]);
    }
    callHook(vm, 'activated');
  }
}

function deactivateChildComponent (vm, direct) {
  if (direct) {
    vm._directInactive = true;
    if (isInInactiveTree(vm)) {
      return
    }
  }
  if (!vm._inactive) {
    vm._inactive = true;
    for (var i = 0; i < vm.$children.length; i++) {
      deactivateChildComponent(vm.$children[i]);
    }
    callHook(vm, 'deactivated');
  }
}

function callHook (vm, hook) {
  var handlers = vm.$options[hook];
  if (handlers) {
    for (var i = 0, j = handlers.length; i < j; i++) {
      try {
        handlers[i].call(vm);
      } catch (e) {
        handleError(e, vm, (hook + " hook"));
      }
    }
  }
  if (vm._hasHookEvent) {
    vm.$emit('hook:' + hook);
  }
}

/*  */


var queue = [];
var has = {};
var circular = {};
var waiting = false;
var flushing = false;
var index = 0;

/**
 * Reset the scheduler's state.
 */
function resetSchedulerState () {
  queue.length = 0;
  has = {};
  if (process.env.NODE_ENV !== 'production') {
    circular = {};
  }
  waiting = flushing = false;
}

/**
 * Flush both queues and run the watchers.
 */
function flushSchedulerQueue () {
  flushing = true;
  var watcher, id, vm;

  // Sort queue before flush.
  // This ensures that:
  // 1. Components are updated from parent to child. (because parent is always
  //    created before the child)
  // 2. A component's user watchers are run before its render watcher (because
  //    user watchers are created before the render watcher)
  // 3. If a component is destroyed during a parent component's watcher run,
  //    its watchers can be skipped.
  queue.sort(function (a, b) { return a.id - b.id; });

  // do not cache length because more watchers might be pushed
  // as we run existing watchers
  for (index = 0; index < queue.length; index++) {
    watcher = queue[index];
    id = watcher.id;
    has[id] = null;
    watcher.run();
    // in dev build, check and stop circular updates.
    if (process.env.NODE_ENV !== 'production' && has[id] != null) {
      circular[id] = (circular[id] || 0) + 1;
      if (circular[id] > config._maxUpdateCount) {
        warn(
          'You may have an infinite update loop ' + (
            watcher.user
              ? ("in watcher with expression \"" + (watcher.expression) + "\"")
              : "in a component render function."
          ),
          watcher.vm
        );
        break
      }
    }
  }

  // reset scheduler before updated hook called
  var oldQueue = queue.slice();
  resetSchedulerState();

  // call updated hooks
  index = oldQueue.length;
  while (index--) {
    watcher = oldQueue[index];
    vm = watcher.vm;
    if (vm._watcher === watcher && vm._isMounted) {
      callHook(vm, 'updated');
    }
  }

  // devtool hook
  /* istanbul ignore if */
  if (devtools && config.devtools) {
    devtools.emit('flush');
  }
}

/**
 * Push a watcher into the watcher queue.
 * Jobs with duplicate IDs will be skipped unless it's
 * pushed when the queue is being flushed.
 */
function queueWatcher (watcher) {
  var id = watcher.id;
  if (has[id] == null) {
    has[id] = true;
    if (!flushing) {
      queue.push(watcher);
    } else {
      // if already flushing, splice the watcher based on its id
      // if already past its id, it will be run next immediately.
      var i = queue.length - 1;
      while (i >= 0 && queue[i].id > watcher.id) {
        i--;
      }
      queue.splice(Math.max(i, index) + 1, 0, watcher);
    }
    // queue the flush
    if (!waiting) {
      waiting = true;
      nextTick(flushSchedulerQueue);
    }
  }
}

/*  */

var uid$2 = 0;

/**
 * A watcher parses an expression, collects dependencies,
 * and fires callback when the expression value changes.
 * This is used for both the $watch() api and directives.
 */
var Watcher = function Watcher (
  vm,
  expOrFn,
  cb,
  options
) {
  this.vm = vm;
  vm._watchers.push(this);
  // options
  if (options) {
    this.deep = !!options.deep;
    this.user = !!options.user;
    this.lazy = !!options.lazy;
    this.sync = !!options.sync;
  } else {
    this.deep = this.user = this.lazy = this.sync = false;
  }
  this.cb = cb;
  this.id = ++uid$2; // uid for batching
  this.active = true;
  this.dirty = this.lazy; // for lazy watchers
  this.deps = [];
  this.newDeps = [];
  this.depIds = new _Set();
  this.newDepIds = new _Set();
  this.expression = process.env.NODE_ENV !== 'production'
    ? expOrFn.toString()
    : '';
  // parse expression for getter
  if (typeof expOrFn === 'function') {
    this.getter = expOrFn;
  } else {
    this.getter = parsePath(expOrFn);
    if (!this.getter) {
      this.getter = function () {};
      process.env.NODE_ENV !== 'production' && warn(
        "Failed watching path: \"" + expOrFn + "\" " +
        'Watcher only accepts simple dot-delimited paths. ' +
        'For full control, use a function instead.',
        vm
      );
    }
  }
  this.value = this.lazy
    ? undefined
    : this.get();
};

/**
 * Evaluate the getter, and re-collect dependencies.
 */
Watcher.prototype.get = function get () {
  pushTarget(this);
  var value;
  var vm = this.vm;
  if (this.user) {
    try {
      value = this.getter.call(vm, vm);
    } catch (e) {
      handleError(e, vm, ("getter for watcher \"" + (this.expression) + "\""));
    }
  } else {
    value = this.getter.call(vm, vm);
  }
  // "touch" every property so they are all tracked as
  // dependencies for deep watching
  if (this.deep) {
    traverse(value);
  }
  popTarget();
  this.cleanupDeps();
  return value
};

/**
 * Add a dependency to this directive.
 */
Watcher.prototype.addDep = function addDep (dep) {
  var id = dep.id;
  if (!this.newDepIds.has(id)) {
    this.newDepIds.add(id);
    this.newDeps.push(dep);
    if (!this.depIds.has(id)) {
      dep.addSub(this);
    }
  }
};

/**
 * Clean up for dependency collection.
 */
Watcher.prototype.cleanupDeps = function cleanupDeps () {
    var this$1 = this;

  var i = this.deps.length;
  while (i--) {
    var dep = this$1.deps[i];
    if (!this$1.newDepIds.has(dep.id)) {
      dep.removeSub(this$1);
    }
  }
  var tmp = this.depIds;
  this.depIds = this.newDepIds;
  this.newDepIds = tmp;
  this.newDepIds.clear();
  tmp = this.deps;
  this.deps = this.newDeps;
  this.newDeps = tmp;
  this.newDeps.length = 0;
};

/**
 * Subscriber interface.
 * Will be called when a dependency changes.
 */
Watcher.prototype.update = function update () {
  /* istanbul ignore else */
  if (this.lazy) {
    this.dirty = true;
  } else if (this.sync) {
    this.run();
  } else {
    queueWatcher(this);
  }
};

/**
 * Scheduler job interface.
 * Will be called by the scheduler.
 */
Watcher.prototype.run = function run () {
  if (this.active) {
    var value = this.get();
    if (
      value !== this.value ||
      // Deep watchers and watchers on Object/Arrays should fire even
      // when the value is the same, because the value may
      // have mutated.
      isObject(value) ||
      this.deep
    ) {
      // set new value
      var oldValue = this.value;
      this.value = value;
      if (this.user) {
        try {
          this.cb.call(this.vm, value, oldValue);
        } catch (e) {
          handleError(e, this.vm, ("callback for watcher \"" + (this.expression) + "\""));
        }
      } else {
        this.cb.call(this.vm, value, oldValue);
      }
    }
  }
};

/**
 * Evaluate the value of the watcher.
 * This only gets called for lazy watchers.
 */
Watcher.prototype.evaluate = function evaluate () {
  this.value = this.get();
  this.dirty = false;
};

/**
 * Depend on all deps collected by this watcher.
 */
Watcher.prototype.depend = function depend () {
    var this$1 = this;

  var i = this.deps.length;
  while (i--) {
    this$1.deps[i].depend();
  }
};

/**
 * Remove self from all dependencies' subscriber list.
 */
Watcher.prototype.teardown = function teardown () {
    var this$1 = this;

  if (this.active) {
    // remove self from vm's watcher list
    // this is a somewhat expensive operation so we skip it
    // if the vm is being destroyed.
    if (!this.vm._isBeingDestroyed) {
      remove(this.vm._watchers, this);
    }
    var i = this.deps.length;
    while (i--) {
      this$1.deps[i].removeSub(this$1);
    }
    this.active = false;
  }
};

/**
 * Recursively traverse an object to evoke all converted
 * getters, so that every nested property inside the object
 * is collected as a "deep" dependency.
 */
var seenObjects = new _Set();
function traverse (val) {
  seenObjects.clear();
  _traverse(val, seenObjects);
}

function _traverse (val, seen) {
  var i, keys;
  var isA = Array.isArray(val);
  if ((!isA && !isObject(val)) || !Object.isExtensible(val)) {
    return
  }
  if (val.__ob__) {
    var depId = val.__ob__.dep.id;
    if (seen.has(depId)) {
      return
    }
    seen.add(depId);
  }
  if (isA) {
    i = val.length;
    while (i--) { _traverse(val[i], seen); }
  } else {
    keys = Object.keys(val);
    i = keys.length;
    while (i--) { _traverse(val[keys[i]], seen); }
  }
}

/*  */

var sharedPropertyDefinition = {
  enumerable: true,
  configurable: true,
  get: noop,
  set: noop
};

function proxy (target, sourceKey, key) {
  sharedPropertyDefinition.get = function proxyGetter () {
    return this[sourceKey][key]
  };
  sharedPropertyDefinition.set = function proxySetter (val) {
    this[sourceKey][key] = val;
  };
  Object.defineProperty(target, key, sharedPropertyDefinition);
}

function initState (vm) {
  vm._watchers = [];
  var opts = vm.$options;
  if (opts.props) { initProps(vm, opts.props); }
  if (opts.methods) { initMethods(vm, opts.methods); }
  if (opts.data) {
    initData(vm);
  } else {
    observe(vm._data = {}, true /* asRootData */);
  }
  if (opts.computed) { initComputed(vm, opts.computed); }
  if (opts.watch) { initWatch(vm, opts.watch); }
}

var isReservedProp = { key: 1, ref: 1, slot: 1 };

function initProps (vm, propsOptions) {
  var propsData = vm.$options.propsData || {};
  var props = vm._props = {};
  // cache prop keys so that future props updates can iterate using Array
  // instead of dynamic object key enumeration.
  var keys = vm.$options._propKeys = [];
  var isRoot = !vm.$parent;
  // root instance props should be converted
  observerState.shouldConvert = isRoot;
  var loop = function ( key ) {
    keys.push(key);
    var value = validateProp(key, propsOptions, propsData, vm);
    /* istanbul ignore else */
    if (process.env.NODE_ENV !== 'production') {
      if (isReservedProp[key]) {
        warn(
          ("\"" + key + "\" is a reserved attribute and cannot be used as component prop."),
          vm
        );
      }
      defineReactive$$1(props, key, value, function () {
        if (vm.$parent && !observerState.isSettingProps) {
          warn(
            "Avoid mutating a prop directly since the value will be " +
            "overwritten whenever the parent component re-renders. " +
            "Instead, use a data or computed property based on the prop's " +
            "value. Prop being mutated: \"" + key + "\"",
            vm
          );
        }
      });
    } else {
      defineReactive$$1(props, key, value);
    }
    // static props are already proxied on the component's prototype
    // during Vue.extend(). We only need to proxy props defined at
    // instantiation here.
    if (!(key in vm)) {
      proxy(vm, "_props", key);
    }
  };

  for (var key in propsOptions) loop( key );
  observerState.shouldConvert = true;
}

function initData (vm) {
  var data = vm.$options.data;
  data = vm._data = typeof data === 'function'
    ? getData(data, vm)
    : data || {};
  if (!isPlainObject(data)) {
    data = {};
    process.env.NODE_ENV !== 'production' && warn(
      'data functions should return an object:\n' +
      'https://vuejs.org/v2/guide/components.html#data-Must-Be-a-Function',
      vm
    );
  }
  // proxy data on instance
  var keys = Object.keys(data);
  var props = vm.$options.props;
  var i = keys.length;
  while (i--) {
    if (props && hasOwn(props, keys[i])) {
      process.env.NODE_ENV !== 'production' && warn(
        "The data property \"" + (keys[i]) + "\" is already declared as a prop. " +
        "Use prop default value instead.",
        vm
      );
    } else if (!isReserved(keys[i])) {
      proxy(vm, "_data", keys[i]);
    }
  }
  // observe data
  observe(data, true /* asRootData */);
}

function getData (data, vm) {
  try {
    return data.call(vm)
  } catch (e) {
    handleError(e, vm, "data()");
    return {}
  }
}

var computedWatcherOptions = { lazy: true };

function initComputed (vm, computed) {
  var watchers = vm._computedWatchers = Object.create(null);

  for (var key in computed) {
    var userDef = computed[key];
    var getter = typeof userDef === 'function' ? userDef : userDef.get;
    if (process.env.NODE_ENV !== 'production') {
      if (getter === undefined) {
        warn(
          ("No getter function has been defined for computed property \"" + key + "\"."),
          vm
        );
        getter = noop;
      }
    }
    // create internal watcher for the computed property.
    watchers[key] = new Watcher(vm, getter, noop, computedWatcherOptions);

    // component-defined computed properties are already defined on the
    // component prototype. We only need to define computed properties defined
    // at instantiation here.
    if (!(key in vm)) {
      defineComputed(vm, key, userDef);
    }
  }
}

function defineComputed (target, key, userDef) {
  if (typeof userDef === 'function') {
    sharedPropertyDefinition.get = createComputedGetter(key);
    sharedPropertyDefinition.set = noop;
  } else {
    sharedPropertyDefinition.get = userDef.get
      ? userDef.cache !== false
        ? createComputedGetter(key)
        : userDef.get
      : noop;
    sharedPropertyDefinition.set = userDef.set
      ? userDef.set
      : noop;
  }
  Object.defineProperty(target, key, sharedPropertyDefinition);
}

function createComputedGetter (key) {
  return function computedGetter () {
    var watcher = this._computedWatchers && this._computedWatchers[key];
    if (watcher) {
      if (watcher.dirty) {
        watcher.evaluate();
      }
      if (Dep.target) {
        watcher.depend();
      }
      return watcher.value
    }
  }
}

function initMethods (vm, methods) {
  var props = vm.$options.props;
  for (var key in methods) {
    vm[key] = methods[key] == null ? noop : bind(methods[key], vm);
    if (process.env.NODE_ENV !== 'production') {
      if (methods[key] == null) {
        warn(
          "method \"" + key + "\" has an undefined value in the component definition. " +
          "Did you reference the function correctly?",
          vm
        );
      }
      if (props && hasOwn(props, key)) {
        warn(
          ("method \"" + key + "\" has already been defined as a prop."),
          vm
        );
      }
    }
  }
}

function initWatch (vm, watch) {
  for (var key in watch) {
    var handler = watch[key];
    if (Array.isArray(handler)) {
      for (var i = 0; i < handler.length; i++) {
        createWatcher(vm, key, handler[i]);
      }
    } else {
      createWatcher(vm, key, handler);
    }
  }
}

function createWatcher (vm, key, handler) {
  var options;
  if (isPlainObject(handler)) {
    options = handler;
    handler = handler.handler;
  }
  if (typeof handler === 'string') {
    handler = vm[handler];
  }
  vm.$watch(key, handler, options);
}

function stateMixin (Vue) {
  // flow somehow has problems with directly declared definition object
  // when using Object.defineProperty, so we have to procedurally build up
  // the object here.
  var dataDef = {};
  dataDef.get = function () { return this._data };
  var propsDef = {};
  propsDef.get = function () { return this._props };
  if (process.env.NODE_ENV !== 'production') {
    dataDef.set = function (newData) {
      warn(
        'Avoid replacing instance root $data. ' +
        'Use nested data properties instead.',
        this
      );
    };
    propsDef.set = function () {
      warn("$props is readonly.", this);
    };
  }
  Object.defineProperty(Vue.prototype, '$data', dataDef);
  Object.defineProperty(Vue.prototype, '$props', propsDef);

  Vue.prototype.$set = set;
  Vue.prototype.$delete = del;

  Vue.prototype.$watch = function (
    expOrFn,
    cb,
    options
  ) {
    var vm = this;
    options = options || {};
    options.user = true;
    var watcher = new Watcher(vm, expOrFn, cb, options);
    if (options.immediate) {
      cb.call(vm, watcher.value);
    }
    return function unwatchFn () {
      watcher.teardown();
    }
  };
}

/*  */

// hooks to be invoked on component VNodes during patch
var componentVNodeHooks = {
  init: function init (
    vnode,
    hydrating,
    parentElm,
    refElm
  ) {
    if (!vnode.componentInstance || vnode.componentInstance._isDestroyed) {
      var child = vnode.componentInstance = createComponentInstanceForVnode(
        vnode,
        activeInstance,
        parentElm,
        refElm
      );
      child.$mount(hydrating ? vnode.elm : undefined, hydrating);
    } else if (vnode.data.keepAlive) {
      // kept-alive components, treat as a patch
      var mountedNode = vnode; // work around flow
      componentVNodeHooks.prepatch(mountedNode, mountedNode);
    }
  },

  prepatch: function prepatch (oldVnode, vnode) {
    var options = vnode.componentOptions;
    var child = vnode.componentInstance = oldVnode.componentInstance;
    updateChildComponent(
      child,
      options.propsData, // updated props
      options.listeners, // updated listeners
      vnode, // new parent vnode
      options.children // new children
    );
  },

  insert: function insert (vnode) {
    if (!vnode.componentInstance._isMounted) {
      vnode.componentInstance._isMounted = true;
      callHook(vnode.componentInstance, 'mounted');
    }
    if (vnode.data.keepAlive) {
      activateChildComponent(vnode.componentInstance, true /* direct */);
    }
  },

  destroy: function destroy (vnode) {
    if (!vnode.componentInstance._isDestroyed) {
      if (!vnode.data.keepAlive) {
        vnode.componentInstance.$destroy();
      } else {
        deactivateChildComponent(vnode.componentInstance, true /* direct */);
      }
    }
  }
};

var hooksToMerge = Object.keys(componentVNodeHooks);

function createComponent (
  Ctor,
  data,
  context,
  children,
  tag
) {
  if (!Ctor) {
    return
  }

  var baseCtor = context.$options._base;
  if (isObject(Ctor)) {
    Ctor = baseCtor.extend(Ctor);
  }

  if (typeof Ctor !== 'function') {
    if (process.env.NODE_ENV !== 'production') {
      warn(("Invalid Component definition: " + (String(Ctor))), context);
    }
    return
  }

  // async component
  if (!Ctor.cid) {
    if (Ctor.resolved) {
      Ctor = Ctor.resolved;
    } else {
      Ctor = resolveAsyncComponent(Ctor, baseCtor, function () {
        // it's ok to queue this on every render because
        // $forceUpdate is buffered by the scheduler.
        context.$forceUpdate();
      });
      if (!Ctor) {
        // return nothing if this is indeed an async component
        // wait for the callback to trigger parent update.
        return
      }
    }
  }

  // resolve constructor options in case global mixins are applied after
  // component constructor creation
  resolveConstructorOptions(Ctor);

  data = data || {};

  // transform component v-model data into props & events
  if (data.model) {
    transformModel(Ctor.options, data);
  }

  // extract props
  var propsData = extractProps(data, Ctor, tag);

  // functional component
  if (Ctor.options.functional) {
    return createFunctionalComponent(Ctor, propsData, data, context, children)
  }

  // extract listeners, since these needs to be treated as
  // child component listeners instead of DOM listeners
  var listeners = data.on;
  // replace with listeners with .native modifier
  data.on = data.nativeOn;

  if (Ctor.options.abstract) {
    // abstract components do not keep anything
    // other than props & listeners
    data = {};
  }

  // merge component management hooks onto the placeholder node
  mergeHooks(data);

  // return a placeholder vnode
  var name = Ctor.options.name || tag;
  var vnode = new VNode(
    ("vue-component-" + (Ctor.cid) + (name ? ("-" + name) : '')),
    data, undefined, undefined, undefined, context,
    { Ctor: Ctor, propsData: propsData, listeners: listeners, tag: tag, children: children }
  );
  return vnode
}

function createFunctionalComponent (
  Ctor,
  propsData,
  data,
  context,
  children
) {
  var props = {};
  var propOptions = Ctor.options.props;
  if (propOptions) {
    for (var key in propOptions) {
      props[key] = validateProp(key, propOptions, propsData);
    }
  }
  // ensure the createElement function in functional components
  // gets a unique context - this is necessary for correct named slot check
  var _context = Object.create(context);
  var h = function (a, b, c, d) { return createElement(_context, a, b, c, d, true); };
  var vnode = Ctor.options.render.call(null, h, {
    props: props,
    data: data,
    parent: context,
    children: children,
    slots: function () { return resolveSlots(children, context); }
  });
  if (vnode instanceof VNode) {
    vnode.functionalContext = context;
    if (data.slot) {
      (vnode.data || (vnode.data = {})).slot = data.slot;
    }
  }
  return vnode
}

function createComponentInstanceForVnode (
  vnode, // we know it's MountedComponentVNode but flow doesn't
  parent, // activeInstance in lifecycle state
  parentElm,
  refElm
) {
  var vnodeComponentOptions = vnode.componentOptions;
  var options = {
    _isComponent: true,
    parent: parent,
    propsData: vnodeComponentOptions.propsData,
    _componentTag: vnodeComponentOptions.tag,
    _parentVnode: vnode,
    _parentListeners: vnodeComponentOptions.listeners,
    _renderChildren: vnodeComponentOptions.children,
    _parentElm: parentElm || null,
    _refElm: refElm || null
  };
  // check inline-template render functions
  var inlineTemplate = vnode.data.inlineTemplate;
  if (inlineTemplate) {
    options.render = inlineTemplate.render;
    options.staticRenderFns = inlineTemplate.staticRenderFns;
  }
  return new vnodeComponentOptions.Ctor(options)
}

function resolveAsyncComponent (
  factory,
  baseCtor,
  cb
) {
  if (factory.requested) {
    // pool callbacks
    factory.pendingCallbacks.push(cb);
  } else {
    factory.requested = true;
    var cbs = factory.pendingCallbacks = [cb];
    var sync = true;

    var resolve = function (res) {
      if (isObject(res)) {
        res = baseCtor.extend(res);
      }
      // cache resolved
      factory.resolved = res;
      // invoke callbacks only if this is not a synchronous resolve
      // (async resolves are shimmed as synchronous during SSR)
      if (!sync) {
        for (var i = 0, l = cbs.length; i < l; i++) {
          cbs[i](res);
        }
      }
    };

    var reject = function (reason) {
      process.env.NODE_ENV !== 'production' && warn(
        "Failed to resolve async component: " + (String(factory)) +
        (reason ? ("\nReason: " + reason) : '')
      );
    };

    var res = factory(resolve, reject);

    // handle promise
    if (res && typeof res.then === 'function' && !factory.resolved) {
      res.then(resolve, reject);
    }

    sync = false;
    // return in case resolved synchronously
    return factory.resolved
  }
}

function extractProps (data, Ctor, tag) {
  // we are only extracting raw values here.
  // validation and default values are handled in the child
  // component itself.
  var propOptions = Ctor.options.props;
  if (!propOptions) {
    return
  }
  var res = {};
  var attrs = data.attrs;
  var props = data.props;
  var domProps = data.domProps;
  if (attrs || props || domProps) {
    for (var key in propOptions) {
      var altKey = hyphenate(key);
      if (process.env.NODE_ENV !== 'production') {
        var keyInLowerCase = key.toLowerCase();
        if (
          key !== keyInLowerCase &&
          attrs && attrs.hasOwnProperty(keyInLowerCase)
        ) {
          tip(
            "Prop \"" + keyInLowerCase + "\" is passed to component " +
            (formatComponentName(tag || Ctor)) + ", but the declared prop name is" +
            " \"" + key + "\". " +
            "Note that HTML attributes are case-insensitive and camelCased " +
            "props need to use their kebab-case equivalents when using in-DOM " +
            "templates. You should probably use \"" + altKey + "\" instead of \"" + key + "\"."
          );
        }
      }
      checkProp(res, props, key, altKey, true) ||
      checkProp(res, attrs, key, altKey) ||
      checkProp(res, domProps, key, altKey);
    }
  }
  return res
}

function checkProp (
  res,
  hash,
  key,
  altKey,
  preserve
) {
  if (hash) {
    if (hasOwn(hash, key)) {
      res[key] = hash[key];
      if (!preserve) {
        delete hash[key];
      }
      return true
    } else if (hasOwn(hash, altKey)) {
      res[key] = hash[altKey];
      if (!preserve) {
        delete hash[altKey];
      }
      return true
    }
  }
  return false
}

function mergeHooks (data) {
  if (!data.hook) {
    data.hook = {};
  }
  for (var i = 0; i < hooksToMerge.length; i++) {
    var key = hooksToMerge[i];
    var fromParent = data.hook[key];
    var ours = componentVNodeHooks[key];
    data.hook[key] = fromParent ? mergeHook$1(ours, fromParent) : ours;
  }
}

function mergeHook$1 (one, two) {
  return function (a, b, c, d) {
    one(a, b, c, d);
    two(a, b, c, d);
  }
}

// transform component v-model info (value and callback) into
// prop and event handler respectively.
function transformModel (options, data) {
  var prop = (options.model && options.model.prop) || 'value';
  var event = (options.model && options.model.event) || 'input';(data.props || (data.props = {}))[prop] = data.model.value;
  var on = data.on || (data.on = {});
  if (on[event]) {
    on[event] = [data.model.callback].concat(on[event]);
  } else {
    on[event] = data.model.callback;
  }
}

/*  */

var SIMPLE_NORMALIZE = 1;
var ALWAYS_NORMALIZE = 2;

// wrapper function for providing a more flexible interface
// without getting yelled at by flow
function createElement (
  context,
  tag,
  data,
  children,
  normalizationType,
  alwaysNormalize
) {
  if (Array.isArray(data) || isPrimitive(data)) {
    normalizationType = children;
    children = data;
    data = undefined;
  }
  if (alwaysNormalize) { normalizationType = ALWAYS_NORMALIZE; }
  return _createElement(context, tag, data, children, normalizationType)
}

function _createElement (
  context,
  tag,
  data,
  children,
  normalizationType
) {
  if (data && data.__ob__) {
    process.env.NODE_ENV !== 'production' && warn(
      "Avoid using observed data object as vnode data: " + (JSON.stringify(data)) + "\n" +
      'Always create fresh vnode data objects in each render!',
      context
    );
    return createEmptyVNode()
  }
  if (!tag) {
    // in case of component :is set to falsy value
    return createEmptyVNode()
  }
  // support single function children as default scoped slot
  if (Array.isArray(children) &&
      typeof children[0] === 'function') {
    data = data || {};
    data.scopedSlots = { default: children[0] };
    children.length = 0;
  }
  if (normalizationType === ALWAYS_NORMALIZE) {
    children = normalizeChildren(children);
  } else if (normalizationType === SIMPLE_NORMALIZE) {
    children = simpleNormalizeChildren(children);
  }
  var vnode, ns;
  if (typeof tag === 'string') {
    var Ctor;
    ns = config.getTagNamespace(tag);
    if (config.isReservedTag(tag)) {
      // platform built-in elements
      vnode = new VNode(
        config.parsePlatformTagName(tag), data, children,
        undefined, undefined, context
      );
    } else if ((Ctor = resolveAsset(context.$options, 'components', tag))) {
      // component
      vnode = createComponent(Ctor, data, context, children, tag);
    } else {
      // unknown or unlisted namespaced elements
      // check at runtime because it may get assigned a namespace when its
      // parent normalizes children
      vnode = new VNode(
        tag, data, children,
        undefined, undefined, context
      );
    }
  } else {
    // direct component options / constructor
    vnode = createComponent(tag, data, context, children);
  }
  if (vnode) {
    if (ns) { applyNS(vnode, ns); }
    return vnode
  } else {
    return createEmptyVNode()
  }
}

function applyNS (vnode, ns) {
  vnode.ns = ns;
  if (vnode.tag === 'foreignObject') {
    // use default namespace inside foreignObject
    return
  }
  if (vnode.children) {
    for (var i = 0, l = vnode.children.length; i < l; i++) {
      var child = vnode.children[i];
      if (child.tag && !child.ns) {
        applyNS(child, ns);
      }
    }
  }
}

/*  */

/**
 * Runtime helper for rendering v-for lists.
 */
function renderList (
  val,
  render
) {
  var ret, i, l, keys, key;
  if (Array.isArray(val) || typeof val === 'string') {
    ret = new Array(val.length);
    for (i = 0, l = val.length; i < l; i++) {
      ret[i] = render(val[i], i);
    }
  } else if (typeof val === 'number') {
    ret = new Array(val);
    for (i = 0; i < val; i++) {
      ret[i] = render(i + 1, i);
    }
  } else if (isObject(val)) {
    keys = Object.keys(val);
    ret = new Array(keys.length);
    for (i = 0, l = keys.length; i < l; i++) {
      key = keys[i];
      ret[i] = render(val[key], key, i);
    }
  }
  return ret
}

/*  */

/**
 * Runtime helper for rendering <slot>
 */
function renderSlot (
  name,
  fallback,
  props,
  bindObject
) {
  var scopedSlotFn = this.$scopedSlots[name];
  if (scopedSlotFn) { // scoped slot
    props = props || {};
    if (bindObject) {
      extend(props, bindObject);
    }
    return scopedSlotFn(props) || fallback
  } else {
    var slotNodes = this.$slots[name];
    // warn duplicate slot usage
    if (slotNodes && process.env.NODE_ENV !== 'production') {
      slotNodes._rendered && warn(
        "Duplicate presence of slot \"" + name + "\" found in the same render tree " +
        "- this will likely cause render errors.",
        this
      );
      slotNodes._rendered = true;
    }
    return slotNodes || fallback
  }
}

/*  */

/**
 * Runtime helper for resolving filters
 */
function resolveFilter (id) {
  return resolveAsset(this.$options, 'filters', id, true) || identity
}

/*  */

/**
 * Runtime helper for checking keyCodes from config.
 */
function checkKeyCodes (
  eventKeyCode,
  key,
  builtInAlias
) {
  var keyCodes = config.keyCodes[key] || builtInAlias;
  if (Array.isArray(keyCodes)) {
    return keyCodes.indexOf(eventKeyCode) === -1
  } else {
    return keyCodes !== eventKeyCode
  }
}

/*  */

/**
 * Runtime helper for merging v-bind="object" into a VNode's data.
 */
function bindObjectProps (
  data,
  tag,
  value,
  asProp
) {
  if (value) {
    if (!isObject(value)) {
      process.env.NODE_ENV !== 'production' && warn(
        'v-bind without argument expects an Object or Array value',
        this
      );
    } else {
      if (Array.isArray(value)) {
        value = toObject(value);
      }
      var hash;
      for (var key in value) {
        if (key === 'class' || key === 'style') {
          hash = data;
        } else {
          var type = data.attrs && data.attrs.type;
          hash = asProp || config.mustUseProp(tag, type, key)
            ? data.domProps || (data.domProps = {})
            : data.attrs || (data.attrs = {});
        }
        if (!(key in hash)) {
          hash[key] = value[key];
        }
      }
    }
  }
  return data
}

/*  */

/**
 * Runtime helper for rendering static trees.
 */
function renderStatic (
  index,
  isInFor
) {
  var tree = this._staticTrees[index];
  // if has already-rendered static tree and not inside v-for,
  // we can reuse the same tree by doing a shallow clone.
  if (tree && !isInFor) {
    return Array.isArray(tree)
      ? cloneVNodes(tree)
      : cloneVNode(tree)
  }
  // otherwise, render a fresh tree.
  tree = this._staticTrees[index] =
    this.$options.staticRenderFns[index].call(this._renderProxy);
  markStatic(tree, ("__static__" + index), false);
  return tree
}

/**
 * Runtime helper for v-once.
 * Effectively it means marking the node as static with a unique key.
 */
function markOnce (
  tree,
  index,
  key
) {
  markStatic(tree, ("__once__" + index + (key ? ("_" + key) : "")), true);
  return tree
}

function markStatic (
  tree,
  key,
  isOnce
) {
  if (Array.isArray(tree)) {
    for (var i = 0; i < tree.length; i++) {
      if (tree[i] && typeof tree[i] !== 'string') {
        markStaticNode(tree[i], (key + "_" + i), isOnce);
      }
    }
  } else {
    markStaticNode(tree, key, isOnce);
  }
}

function markStaticNode (node, key, isOnce) {
  node.isStatic = true;
  node.key = key;
  node.isOnce = isOnce;
}

/*  */

function initRender (vm) {
  vm.$vnode = null; // the placeholder node in parent tree
  vm._vnode = null; // the root of the child tree
  vm._staticTrees = null;
  var parentVnode = vm.$options._parentVnode;
  var renderContext = parentVnode && parentVnode.context;
  vm.$slots = resolveSlots(vm.$options._renderChildren, renderContext);
  vm.$scopedSlots = emptyObject;
  // bind the createElement fn to this instance
  // so that we get proper render context inside it.
  // args order: tag, data, children, normalizationType, alwaysNormalize
  // internal version is used by render functions compiled from templates
  vm._c = function (a, b, c, d) { return createElement(vm, a, b, c, d, false); };
  // normalization is always applied for the public version, used in
  // user-written render functions.
  vm.$createElement = function (a, b, c, d) { return createElement(vm, a, b, c, d, true); };
}

function renderMixin (Vue) {
  Vue.prototype.$nextTick = function (fn) {
    return nextTick(fn, this)
  };

  Vue.prototype._render = function () {
    var vm = this;
    var ref = vm.$options;
    var render = ref.render;
    var staticRenderFns = ref.staticRenderFns;
    var _parentVnode = ref._parentVnode;

    if (vm._isMounted) {
      // clone slot nodes on re-renders
      for (var key in vm.$slots) {
        vm.$slots[key] = cloneVNodes(vm.$slots[key]);
      }
    }

    vm.$scopedSlots = (_parentVnode && _parentVnode.data.scopedSlots) || emptyObject;

    if (staticRenderFns && !vm._staticTrees) {
      vm._staticTrees = [];
    }
    // set parent vnode. this allows render functions to have access
    // to the data on the placeholder node.
    vm.$vnode = _parentVnode;
    // render self
    var vnode;
    try {
      vnode = render.call(vm._renderProxy, vm.$createElement);
    } catch (e) {
      handleError(e, vm, "render function");
      // return error render result,
      // or previous vnode to prevent render error causing blank component
      /* istanbul ignore else */
      if (process.env.NODE_ENV !== 'production') {
        vnode = vm.$options.renderError
          ? vm.$options.renderError.call(vm._renderProxy, vm.$createElement, e)
          : vm._vnode;
      } else {
        vnode = vm._vnode;
      }
    }
    // return empty vnode in case the render function errored out
    if (!(vnode instanceof VNode)) {
      if (process.env.NODE_ENV !== 'production' && Array.isArray(vnode)) {
        warn(
          'Multiple root nodes returned from render function. Render function ' +
          'should return a single root node.',
          vm
        );
      }
      vnode = createEmptyVNode();
    }
    // set parent
    vnode.parent = _parentVnode;
    return vnode
  };

  // internal render helpers.
  // these are exposed on the instance prototype to reduce generated render
  // code size.
  Vue.prototype._o = markOnce;
  Vue.prototype._n = toNumber;
  Vue.prototype._s = _toString;
  Vue.prototype._l = renderList;
  Vue.prototype._t = renderSlot;
  Vue.prototype._q = looseEqual;
  Vue.prototype._i = looseIndexOf;
  Vue.prototype._m = renderStatic;
  Vue.prototype._f = resolveFilter;
  Vue.prototype._k = checkKeyCodes;
  Vue.prototype._b = bindObjectProps;
  Vue.prototype._v = createTextVNode;
  Vue.prototype._e = createEmptyVNode;
  Vue.prototype._u = resolveScopedSlots;
}

/*  */

function initProvide (vm) {
  var provide = vm.$options.provide;
  if (provide) {
    vm._provided = typeof provide === 'function'
      ? provide.call(vm)
      : provide;
  }
}

function initInjections (vm) {
  var inject = vm.$options.inject;
  if (inject) {
    // inject is :any because flow is not smart enough to figure out cached
    // isArray here
    var isArray = Array.isArray(inject);
    var keys = isArray
      ? inject
      : hasSymbol
        ? Reflect.ownKeys(inject)
        : Object.keys(inject);

    var loop = function ( i ) {
      var key = keys[i];
      var provideKey = isArray ? key : inject[key];
      var source = vm;
      while (source) {
        if (source._provided && provideKey in source._provided) {
          /* istanbul ignore else */
          if (process.env.NODE_ENV !== 'production') {
            defineReactive$$1(vm, key, source._provided[provideKey], function () {
              warn(
                "Avoid mutating an injected value directly since the changes will be " +
                "overwritten whenever the provided component re-renders. " +
                "injection being mutated: \"" + key + "\"",
                vm
              );
            });
          } else {
            defineReactive$$1(vm, key, source._provided[provideKey]);
          }
          break
        }
        source = source.$parent;
      }
    };

    for (var i = 0; i < keys.length; i++) loop( i );
  }
}

/*  */

var uid = 0;

function initMixin (Vue) {
  Vue.prototype._init = function (options) {
    var vm = this;
    // a uid
    vm._uid = uid++;

    var startTag, endTag;
    /* istanbul ignore if */
    if (process.env.NODE_ENV !== 'production' && config.performance && mark) {
      startTag = "vue-perf-init:" + (vm._uid);
      endTag = "vue-perf-end:" + (vm._uid);
      mark(startTag);
    }

    // a flag to avoid this being observed
    vm._isVue = true;
    // merge options
    if (options && options._isComponent) {
      // optimize internal component instantiation
      // since dynamic options merging is pretty slow, and none of the
      // internal component options needs special treatment.
      initInternalComponent(vm, options);
    } else {
      vm.$options = mergeOptions(
        resolveConstructorOptions(vm.constructor),
        options || {},
        vm
      );
    }
    /* istanbul ignore else */
    if (process.env.NODE_ENV !== 'production') {
      initProxy(vm);
    } else {
      vm._renderProxy = vm;
    }
    // expose real self
    vm._self = vm;
    initLifecycle(vm);
    initEvents(vm);
    initRender(vm);
    callHook(vm, 'beforeCreate');
    initInjections(vm); // resolve injections before data/props
    initState(vm);
    initProvide(vm); // resolve provide after data/props
    callHook(vm, 'created');

    /* istanbul ignore if */
    if (process.env.NODE_ENV !== 'production' && config.performance && mark) {
      vm._name = formatComponentName(vm, false);
      mark(endTag);
      measure(((vm._name) + " init"), startTag, endTag);
    }

    if (vm.$options.el) {
      vm.$mount(vm.$options.el);
    }
  };
}

function initInternalComponent (vm, options) {
  var opts = vm.$options = Object.create(vm.constructor.options);
  // doing this because it's faster than dynamic enumeration.
  opts.parent = options.parent;
  opts.propsData = options.propsData;
  opts._parentVnode = options._parentVnode;
  opts._parentListeners = options._parentListeners;
  opts._renderChildren = options._renderChildren;
  opts._componentTag = options._componentTag;
  opts._parentElm = options._parentElm;
  opts._refElm = options._refElm;
  if (options.render) {
    opts.render = options.render;
    opts.staticRenderFns = options.staticRenderFns;
  }
}

function resolveConstructorOptions (Ctor) {
  var options = Ctor.options;
  if (Ctor.super) {
    var superOptions = resolveConstructorOptions(Ctor.super);
    var cachedSuperOptions = Ctor.superOptions;
    if (superOptions !== cachedSuperOptions) {
      // super option changed,
      // need to resolve new options.
      Ctor.superOptions = superOptions;
      // check if there are any late-modified/attached options (#4976)
      var modifiedOptions = resolveModifiedOptions(Ctor);
      // update base extend options
      if (modifiedOptions) {
        extend(Ctor.extendOptions, modifiedOptions);
      }
      options = Ctor.options = mergeOptions(superOptions, Ctor.extendOptions);
      if (options.name) {
        options.components[options.name] = Ctor;
      }
    }
  }
  return options
}

function resolveModifiedOptions (Ctor) {
  var modified;
  var latest = Ctor.options;
  var sealed = Ctor.sealedOptions;
  for (var key in latest) {
    if (latest[key] !== sealed[key]) {
      if (!modified) { modified = {}; }
      modified[key] = dedupe(latest[key], sealed[key]);
    }
  }
  return modified
}

function dedupe (latest, sealed) {
  // compare latest and sealed to ensure lifecycle hooks won't be duplicated
  // between merges
  if (Array.isArray(latest)) {
    var res = [];
    sealed = Array.isArray(sealed) ? sealed : [sealed];
    for (var i = 0; i < latest.length; i++) {
      if (sealed.indexOf(latest[i]) < 0) {
        res.push(latest[i]);
      }
    }
    return res
  } else {
    return latest
  }
}

function Vue$2 (options) {
  if (process.env.NODE_ENV !== 'production' &&
    !(this instanceof Vue$2)) {
    warn('Vue is a constructor and should be called with the `new` keyword');
  }
  this._init(options);
}

initMixin(Vue$2);
stateMixin(Vue$2);
eventsMixin(Vue$2);
lifecycleMixin(Vue$2);
renderMixin(Vue$2);

/*  */

function initUse (Vue) {
  Vue.use = function (plugin) {
    /* istanbul ignore if */
    if (plugin.installed) {
      return
    }
    // additional parameters
    var args = toArray(arguments, 1);
    args.unshift(this);
    if (typeof plugin.install === 'function') {
      plugin.install.apply(plugin, args);
    } else if (typeof plugin === 'function') {
      plugin.apply(null, args);
    }
    plugin.installed = true;
    return this
  };
}

/*  */

function initMixin$1 (Vue) {
  Vue.mixin = function (mixin) {
    this.options = mergeOptions(this.options, mixin);
  };
}

/*  */

function initExtend (Vue) {
  /**
   * Each instance constructor, including Vue, has a unique
   * cid. This enables us to create wrapped "child
   * constructors" for prototypal inheritance and cache them.
   */
  Vue.cid = 0;
  var cid = 1;

  /**
   * Class inheritance
   */
  Vue.extend = function (extendOptions) {
    extendOptions = extendOptions || {};
    var Super = this;
    var SuperId = Super.cid;
    var cachedCtors = extendOptions._Ctor || (extendOptions._Ctor = {});
    if (cachedCtors[SuperId]) {
      return cachedCtors[SuperId]
    }

    var name = extendOptions.name || Super.options.name;
    if (process.env.NODE_ENV !== 'production') {
      if (!/^[a-zA-Z][\w-]*$/.test(name)) {
        warn(
          'Invalid component name: "' + name + '". Component names ' +
          'can only contain alphanumeric characters and the hyphen, ' +
          'and must start with a letter.'
        );
      }
    }

    var Sub = function VueComponent (options) {
      this._init(options);
    };
    Sub.prototype = Object.create(Super.prototype);
    Sub.prototype.constructor = Sub;
    Sub.cid = cid++;
    Sub.options = mergeOptions(
      Super.options,
      extendOptions
    );
    Sub['super'] = Super;

    // For props and computed properties, we define the proxy getters on
    // the Vue instances at extension time, on the extended prototype. This
    // avoids Object.defineProperty calls for each instance created.
    if (Sub.options.props) {
      initProps$1(Sub);
    }
    if (Sub.options.computed) {
      initComputed$1(Sub);
    }

    // allow further extension/mixin/plugin usage
    Sub.extend = Super.extend;
    Sub.mixin = Super.mixin;
    Sub.use = Super.use;

    // create asset registers, so extended classes
    // can have their private assets too.
    config._assetTypes.forEach(function (type) {
      Sub[type] = Super[type];
    });
    // enable recursive self-lookup
    if (name) {
      Sub.options.components[name] = Sub;
    }

    // keep a reference to the super options at extension time.
    // later at instantiation we can check if Super's options have
    // been updated.
    Sub.superOptions = Super.options;
    Sub.extendOptions = extendOptions;
    Sub.sealedOptions = extend({}, Sub.options);

    // cache constructor
    cachedCtors[SuperId] = Sub;
    return Sub
  };
}

function initProps$1 (Comp) {
  var props = Comp.options.props;
  for (var key in props) {
    proxy(Comp.prototype, "_props", key);
  }
}

function initComputed$1 (Comp) {
  var computed = Comp.options.computed;
  for (var key in computed) {
    defineComputed(Comp.prototype, key, computed[key]);
  }
}

/*  */

function initAssetRegisters (Vue) {
  /**
   * Create asset registration methods.
   */
  config._assetTypes.forEach(function (type) {
    Vue[type] = function (
      id,
      definition
    ) {
      if (!definition) {
        return this.options[type + 's'][id]
      } else {
        /* istanbul ignore if */
        if (process.env.NODE_ENV !== 'production') {
          if (type === 'component' && config.isReservedTag(id)) {
            warn(
              'Do not use built-in or reserved HTML elements as component ' +
              'id: ' + id
            );
          }
        }
        if (type === 'component' && isPlainObject(definition)) {
          definition.name = definition.name || id;
          definition = this.options._base.extend(definition);
        }
        if (type === 'directive' && typeof definition === 'function') {
          definition = { bind: definition, update: definition };
        }
        this.options[type + 's'][id] = definition;
        return definition
      }
    };
  });
}

/*  */

var patternTypes = [String, RegExp];

function getComponentName (opts) {
  return opts && (opts.Ctor.options.name || opts.tag)
}

function matches (pattern, name) {
  if (typeof pattern === 'string') {
    return pattern.split(',').indexOf(name) > -1
  } else if (pattern instanceof RegExp) {
    return pattern.test(name)
  }
  /* istanbul ignore next */
  return false
}

function pruneCache (cache, filter) {
  for (var key in cache) {
    var cachedNode = cache[key];
    if (cachedNode) {
      var name = getComponentName(cachedNode.componentOptions);
      if (name && !filter(name)) {
        pruneCacheEntry(cachedNode);
        cache[key] = null;
      }
    }
  }
}

function pruneCacheEntry (vnode) {
  if (vnode) {
    if (!vnode.componentInstance._inactive) {
      callHook(vnode.componentInstance, 'deactivated');
    }
    vnode.componentInstance.$destroy();
  }
}

var KeepAlive = {
  name: 'keep-alive',
  abstract: true,

  props: {
    include: patternTypes,
    exclude: patternTypes
  },

  created: function created () {
    this.cache = Object.create(null);
  },

  destroyed: function destroyed () {
    var this$1 = this;

    for (var key in this$1.cache) {
      pruneCacheEntry(this$1.cache[key]);
    }
  },

  watch: {
    include: function include (val) {
      pruneCache(this.cache, function (name) { return matches(val, name); });
    },
    exclude: function exclude (val) {
      pruneCache(this.cache, function (name) { return !matches(val, name); });
    }
  },

  render: function render () {
    var vnode = getFirstComponentChild(this.$slots.default);
    var componentOptions = vnode && vnode.componentOptions;
    if (componentOptions) {
      // check pattern
      var name = getComponentName(componentOptions);
      if (name && (
        (this.include && !matches(this.include, name)) ||
        (this.exclude && matches(this.exclude, name))
      )) {
        return vnode
      }
      var key = vnode.key == null
        // same constructor may get registered as different local components
        // so cid alone is not enough (#3269)
        ? componentOptions.Ctor.cid + (componentOptions.tag ? ("::" + (componentOptions.tag)) : '')
        : vnode.key;
      if (this.cache[key]) {
        vnode.componentInstance = this.cache[key].componentInstance;
      } else {
        this.cache[key] = vnode;
      }
      vnode.data.keepAlive = true;
    }
    return vnode
  }
};

var builtInComponents = {
  KeepAlive: KeepAlive
};

/*  */

function initGlobalAPI (Vue) {
  // config
  var configDef = {};
  configDef.get = function () { return config; };
  if (process.env.NODE_ENV !== 'production') {
    configDef.set = function () {
      warn(
        'Do not replace the Vue.config object, set individual fields instead.'
      );
    };
  }
  Object.defineProperty(Vue, 'config', configDef);

  // exposed util methods.
  // NOTE: these are not considered part of the public API - avoid relying on
  // them unless you are aware of the risk.
  Vue.util = {
    warn: warn,
    extend: extend,
    mergeOptions: mergeOptions,
    defineReactive: defineReactive$$1
  };

  Vue.set = set;
  Vue.delete = del;
  Vue.nextTick = nextTick;

  Vue.options = Object.create(null);
  config._assetTypes.forEach(function (type) {
    Vue.options[type + 's'] = Object.create(null);
  });

  // this is used to identify the "base" constructor to extend all plain-object
  // components with in Weex's multi-instance scenarios.
  Vue.options._base = Vue;

  extend(Vue.options.components, builtInComponents);

  initUse(Vue);
  initMixin$1(Vue);
  initExtend(Vue);
  initAssetRegisters(Vue);
}

initGlobalAPI(Vue$2);

Object.defineProperty(Vue$2.prototype, '$isServer', {
  get: isServerRendering
});

Vue$2.version = '2.2.6';

/*  */

// attributes that should be using props for binding
var acceptValue = makeMap('input,textarea,option,select');
var mustUseProp = function (tag, type, attr) {
  return (
    (attr === 'value' && acceptValue(tag)) && type !== 'button' ||
    (attr === 'selected' && tag === 'option') ||
    (attr === 'checked' && tag === 'input') ||
    (attr === 'muted' && tag === 'video')
  )
};

var isEnumeratedAttr = makeMap('contenteditable,draggable,spellcheck');

var isBooleanAttr = makeMap(
  'allowfullscreen,async,autofocus,autoplay,checked,compact,controls,declare,' +
  'default,defaultchecked,defaultmuted,defaultselected,defer,disabled,' +
  'enabled,formnovalidate,hidden,indeterminate,inert,ismap,itemscope,loop,multiple,' +
  'muted,nohref,noresize,noshade,novalidate,nowrap,open,pauseonexit,readonly,' +
  'required,reversed,scoped,seamless,selected,sortable,translate,' +
  'truespeed,typemustmatch,visible'
);

var xlinkNS = 'http://www.w3.org/1999/xlink';

var isXlink = function (name) {
  return name.charAt(5) === ':' && name.slice(0, 5) === 'xlink'
};

var getXlinkProp = function (name) {
  return isXlink(name) ? name.slice(6, name.length) : ''
};

var isFalsyAttrValue = function (val) {
  return val == null || val === false
};

/*  */

function genClassForVnode (vnode) {
  var data = vnode.data;
  var parentNode = vnode;
  var childNode = vnode;
  while (childNode.componentInstance) {
    childNode = childNode.componentInstance._vnode;
    if (childNode.data) {
      data = mergeClassData(childNode.data, data);
    }
  }
  while ((parentNode = parentNode.parent)) {
    if (parentNode.data) {
      data = mergeClassData(data, parentNode.data);
    }
  }
  return genClassFromData(data)
}

function mergeClassData (child, parent) {
  return {
    staticClass: concat(child.staticClass, parent.staticClass),
    class: child.class
      ? [child.class, parent.class]
      : parent.class
  }
}

function genClassFromData (data) {
  var dynamicClass = data.class;
  var staticClass = data.staticClass;
  if (staticClass || dynamicClass) {
    return concat(staticClass, stringifyClass(dynamicClass))
  }
  /* istanbul ignore next */
  return ''
}

function concat (a, b) {
  return a ? b ? (a + ' ' + b) : a : (b || '')
}

function stringifyClass (value) {
  var res = '';
  if (!value) {
    return res
  }
  if (typeof value === 'string') {
    return value
  }
  if (Array.isArray(value)) {
    var stringified;
    for (var i = 0, l = value.length; i < l; i++) {
      if (value[i]) {
        if ((stringified = stringifyClass(value[i]))) {
          res += stringified + ' ';
        }
      }
    }
    return res.slice(0, -1)
  }
  if (isObject(value)) {
    for (var key in value) {
      if (value[key]) { res += key + ' '; }
    }
    return res.slice(0, -1)
  }
  /* istanbul ignore next */
  return res
}

/*  */

var namespaceMap = {
  svg: 'http://www.w3.org/2000/svg',
  math: 'http://www.w3.org/1998/Math/MathML'
};

var isHTMLTag = makeMap(
  'html,body,base,head,link,meta,style,title,' +
  'address,article,aside,footer,header,h1,h2,h3,h4,h5,h6,hgroup,nav,section,' +
  'div,dd,dl,dt,figcaption,figure,hr,img,li,main,ol,p,pre,ul,' +
  'a,b,abbr,bdi,bdo,br,cite,code,data,dfn,em,i,kbd,mark,q,rp,rt,rtc,ruby,' +
  's,samp,small,span,strong,sub,sup,time,u,var,wbr,area,audio,map,track,video,' +
  'embed,object,param,source,canvas,script,noscript,del,ins,' +
  'caption,col,colgroup,table,thead,tbody,td,th,tr,' +
  'button,datalist,fieldset,form,input,label,legend,meter,optgroup,option,' +
  'output,progress,select,textarea,' +
  'details,dialog,menu,menuitem,summary,' +
  'content,element,shadow,template'
);

// this map is intentionally selective, only covering SVG elements that may
// contain child elements.
var isSVG = makeMap(
  'svg,animate,circle,clippath,cursor,defs,desc,ellipse,filter,font-face,' +
  'foreignObject,g,glyph,image,line,marker,mask,missing-glyph,path,pattern,' +
  'polygon,polyline,rect,switch,symbol,text,textpath,tspan,use,view',
  true
);



var isReservedTag = function (tag) {
  return isHTMLTag(tag) || isSVG(tag)
};

function getTagNamespace (tag) {
  if (isSVG(tag)) {
    return 'svg'
  }
  // basic support for MathML
  // note it doesn't support other MathML elements being component roots
  if (tag === 'math') {
    return 'math'
  }
}

var unknownElementCache = Object.create(null);
function isUnknownElement (tag) {
  /* istanbul ignore if */
  if (!inBrowser) {
    return true
  }
  if (isReservedTag(tag)) {
    return false
  }
  tag = tag.toLowerCase();
  /* istanbul ignore if */
  if (unknownElementCache[tag] != null) {
    return unknownElementCache[tag]
  }
  var el = document.createElement(tag);
  if (tag.indexOf('-') > -1) {
    // http://stackoverflow.com/a/28210364/1070244
    return (unknownElementCache[tag] = (
      el.constructor === window.HTMLUnknownElement ||
      el.constructor === window.HTMLElement
    ))
  } else {
    return (unknownElementCache[tag] = /HTMLUnknownElement/.test(el.toString()))
  }
}

/*  */

/**
 * Query an element selector if it's not an element already.
 */
function query (el) {
  if (typeof el === 'string') {
    var selected = document.querySelector(el);
    if (!selected) {
      process.env.NODE_ENV !== 'production' && warn(
        'Cannot find element: ' + el
      );
      return document.createElement('div')
    }
    return selected
  } else {
    return el
  }
}

/*  */

function createElement$1 (tagName, vnode) {
  var elm = document.createElement(tagName);
  if (tagName !== 'select') {
    return elm
  }
  // false or null will remove the attribute but undefined will not
  if (vnode.data && vnode.data.attrs && vnode.data.attrs.multiple !== undefined) {
    elm.setAttribute('multiple', 'multiple');
  }
  return elm
}

function createElementNS (namespace, tagName) {
  return document.createElementNS(namespaceMap[namespace], tagName)
}

function createTextNode (text) {
  return document.createTextNode(text)
}

function createComment (text) {
  return document.createComment(text)
}

function insertBefore (parentNode, newNode, referenceNode) {
  parentNode.insertBefore(newNode, referenceNode);
}

function removeChild (node, child) {
  node.removeChild(child);
}

function appendChild (node, child) {
  node.appendChild(child);
}

function parentNode (node) {
  return node.parentNode
}

function nextSibling (node) {
  return node.nextSibling
}

function tagName (node) {
  return node.tagName
}

function setTextContent (node, text) {
  node.textContent = text;
}

function setAttribute (node, key, val) {
  node.setAttribute(key, val);
}


var nodeOps = Object.freeze({
	createElement: createElement$1,
	createElementNS: createElementNS,
	createTextNode: createTextNode,
	createComment: createComment,
	insertBefore: insertBefore,
	removeChild: removeChild,
	appendChild: appendChild,
	parentNode: parentNode,
	nextSibling: nextSibling,
	tagName: tagName,
	setTextContent: setTextContent,
	setAttribute: setAttribute
});

/*  */

var ref = {
  create: function create (_, vnode) {
    registerRef(vnode);
  },
  update: function update (oldVnode, vnode) {
    if (oldVnode.data.ref !== vnode.data.ref) {
      registerRef(oldVnode, true);
      registerRef(vnode);
    }
  },
  destroy: function destroy (vnode) {
    registerRef(vnode, true);
  }
};

function registerRef (vnode, isRemoval) {
  var key = vnode.data.ref;
  if (!key) { return }

  var vm = vnode.context;
  var ref = vnode.componentInstance || vnode.elm;
  var refs = vm.$refs;
  if (isRemoval) {
    if (Array.isArray(refs[key])) {
      remove(refs[key], ref);
    } else if (refs[key] === ref) {
      refs[key] = undefined;
    }
  } else {
    if (vnode.data.refInFor) {
      if (Array.isArray(refs[key]) && refs[key].indexOf(ref) < 0) {
        refs[key].push(ref);
      } else {
        refs[key] = [ref];
      }
    } else {
      refs[key] = ref;
    }
  }
}

/**
 * Virtual DOM patching algorithm based on Snabbdom by
 * Simon Friis Vindum (@paldepind)
 * Licensed under the MIT License
 * https://github.com/paldepind/snabbdom/blob/master/LICENSE
 *
 * modified by Evan You (@yyx990803)
 *

/*
 * Not type-checking this because this file is perf-critical and the cost
 * of making flow understand it is not worth it.
 */

var emptyNode = new VNode('', {}, []);

var hooks = ['create', 'activate', 'update', 'remove', 'destroy'];

function isUndef (v) {
  return v === undefined || v === null
}

function isDef (v) {
  return v !== undefined && v !== null
}

function isTrue (v) {
  return v === true
}

function sameVnode (a, b) {
  return (
    a.key === b.key &&
    a.tag === b.tag &&
    a.isComment === b.isComment &&
    isDef(a.data) === isDef(b.data) &&
    sameInputType(a, b)
  )
}

// Some browsers do not support dynamically changing type for <input>
// so they need to be treated as different nodes
function sameInputType (a, b) {
  if (a.tag !== 'input') { return true }
  var i;
  var typeA = isDef(i = a.data) && isDef(i = i.attrs) && i.type;
  var typeB = isDef(i = b.data) && isDef(i = i.attrs) && i.type;
  return typeA === typeB
}

function createKeyToOldIdx (children, beginIdx, endIdx) {
  var i, key;
  var map = {};
  for (i = beginIdx; i <= endIdx; ++i) {
    key = children[i].key;
    if (isDef(key)) { map[key] = i; }
  }
  return map
}

function createPatchFunction (backend) {
  var i, j;
  var cbs = {};

  var modules = backend.modules;
  var nodeOps = backend.nodeOps;

  for (i = 0; i < hooks.length; ++i) {
    cbs[hooks[i]] = [];
    for (j = 0; j < modules.length; ++j) {
      if (isDef(modules[j][hooks[i]])) {
        cbs[hooks[i]].push(modules[j][hooks[i]]);
      }
    }
  }

  function emptyNodeAt (elm) {
    return new VNode(nodeOps.tagName(elm).toLowerCase(), {}, [], undefined, elm)
  }

  function createRmCb (childElm, listeners) {
    function remove$$1 () {
      if (--remove$$1.listeners === 0) {
        removeNode(childElm);
      }
    }
    remove$$1.listeners = listeners;
    return remove$$1
  }

  function removeNode (el) {
    var parent = nodeOps.parentNode(el);
    // element may have already been removed due to v-html / v-text
    if (isDef(parent)) {
      nodeOps.removeChild(parent, el);
    }
  }

  var inPre = 0;
  function createElm (vnode, insertedVnodeQueue, parentElm, refElm, nested) {
    vnode.isRootInsert = !nested; // for transition enter check
    if (createComponent(vnode, insertedVnodeQueue, parentElm, refElm)) {
      return
    }

    var data = vnode.data;
    var children = vnode.children;
    var tag = vnode.tag;
    if (isDef(tag)) {
      if (process.env.NODE_ENV !== 'production') {
        if (data && data.pre) {
          inPre++;
        }
        if (
          !inPre &&
          !vnode.ns &&
          !(config.ignoredElements.length && config.ignoredElements.indexOf(tag) > -1) &&
          config.isUnknownElement(tag)
        ) {
          warn(
            'Unknown custom element: <' + tag + '> - did you ' +
            'register the component correctly? For recursive components, ' +
            'make sure to provide the "name" option.',
            vnode.context
          );
        }
      }
      vnode.elm = vnode.ns
        ? nodeOps.createElementNS(vnode.ns, tag)
        : nodeOps.createElement(tag, vnode);
      setScope(vnode);

      /* istanbul ignore if */
      {
        createChildren(vnode, children, insertedVnodeQueue);
        if (isDef(data)) {
          invokeCreateHooks(vnode, insertedVnodeQueue);
        }
        insert(parentElm, vnode.elm, refElm);
      }

      if (process.env.NODE_ENV !== 'production' && data && data.pre) {
        inPre--;
      }
    } else if (isTrue(vnode.isComment)) {
      vnode.elm = nodeOps.createComment(vnode.text);
      insert(parentElm, vnode.elm, refElm);
    } else {
      vnode.elm = nodeOps.createTextNode(vnode.text);
      insert(parentElm, vnode.elm, refElm);
    }
  }

  function createComponent (vnode, insertedVnodeQueue, parentElm, refElm) {
    var i = vnode.data;
    if (isDef(i)) {
      var isReactivated = isDef(vnode.componentInstance) && i.keepAlive;
      if (isDef(i = i.hook) && isDef(i = i.init)) {
        i(vnode, false /* hydrating */, parentElm, refElm);
      }
      // after calling the init hook, if the vnode is a child component
      // it should've created a child instance and mounted it. the child
      // component also has set the placeholder vnode's elm.
      // in that case we can just return the element and be done.
      if (isDef(vnode.componentInstance)) {
        initComponent(vnode, insertedVnodeQueue);
        if (isTrue(isReactivated)) {
          reactivateComponent(vnode, insertedVnodeQueue, parentElm, refElm);
        }
        return true
      }
    }
  }

  function initComponent (vnode, insertedVnodeQueue) {
    if (isDef(vnode.data.pendingInsert)) {
      insertedVnodeQueue.push.apply(insertedVnodeQueue, vnode.data.pendingInsert);
    }
    vnode.elm = vnode.componentInstance.$el;
    if (isPatchable(vnode)) {
      invokeCreateHooks(vnode, insertedVnodeQueue);
      setScope(vnode);
    } else {
      // empty component root.
      // skip all element-related modules except for ref (#3455)
      registerRef(vnode);
      // make sure to invoke the insert hook
      insertedVnodeQueue.push(vnode);
    }
  }

  function reactivateComponent (vnode, insertedVnodeQueue, parentElm, refElm) {
    var i;
    // hack for #4339: a reactivated component with inner transition
    // does not trigger because the inner node's created hooks are not called
    // again. It's not ideal to involve module-specific logic in here but
    // there doesn't seem to be a better way to do it.
    var innerNode = vnode;
    while (innerNode.componentInstance) {
      innerNode = innerNode.componentInstance._vnode;
      if (isDef(i = innerNode.data) && isDef(i = i.transition)) {
        for (i = 0; i < cbs.activate.length; ++i) {
          cbs.activate[i](emptyNode, innerNode);
        }
        insertedVnodeQueue.push(innerNode);
        break
      }
    }
    // unlike a newly created component,
    // a reactivated keep-alive component doesn't insert itself
    insert(parentElm, vnode.elm, refElm);
  }

  function insert (parent, elm, ref) {
    if (isDef(parent)) {
      if (isDef(ref)) {
        nodeOps.insertBefore(parent, elm, ref);
      } else {
        nodeOps.appendChild(parent, elm);
      }
    }
  }

  function createChildren (vnode, children, insertedVnodeQueue) {
    if (Array.isArray(children)) {
      for (var i = 0; i < children.length; ++i) {
        createElm(children[i], insertedVnodeQueue, vnode.elm, null, true);
      }
    } else if (isPrimitive(vnode.text)) {
      nodeOps.appendChild(vnode.elm, nodeOps.createTextNode(vnode.text));
    }
  }

  function isPatchable (vnode) {
    while (vnode.componentInstance) {
      vnode = vnode.componentInstance._vnode;
    }
    return isDef(vnode.tag)
  }

  function invokeCreateHooks (vnode, insertedVnodeQueue) {
    for (var i$1 = 0; i$1 < cbs.create.length; ++i$1) {
      cbs.create[i$1](emptyNode, vnode);
    }
    i = vnode.data.hook; // Reuse variable
    if (isDef(i)) {
      if (isDef(i.create)) { i.create(emptyNode, vnode); }
      if (isDef(i.insert)) { insertedVnodeQueue.push(vnode); }
    }
  }

  // set scope id attribute for scoped CSS.
  // this is implemented as a special case to avoid the overhead
  // of going through the normal attribute patching process.
  function setScope (vnode) {
    var i;
    var ancestor = vnode;
    while (ancestor) {
      if (isDef(i = ancestor.context) && isDef(i = i.$options._scopeId)) {
        nodeOps.setAttribute(vnode.elm, i, '');
      }
      ancestor = ancestor.parent;
    }
    // for slot content they should also get the scopeId from the host instance.
    if (isDef(i = activeInstance) &&
        i !== vnode.context &&
        isDef(i = i.$options._scopeId)) {
      nodeOps.setAttribute(vnode.elm, i, '');
    }
  }

  function addVnodes (parentElm, refElm, vnodes, startIdx, endIdx, insertedVnodeQueue) {
    for (; startIdx <= endIdx; ++startIdx) {
      createElm(vnodes[startIdx], insertedVnodeQueue, parentElm, refElm);
    }
  }

  function invokeDestroyHook (vnode) {
    var i, j;
    var data = vnode.data;
    if (isDef(data)) {
      if (isDef(i = data.hook) && isDef(i = i.destroy)) { i(vnode); }
      for (i = 0; i < cbs.destroy.length; ++i) { cbs.destroy[i](vnode); }
    }
    if (isDef(i = vnode.children)) {
      for (j = 0; j < vnode.children.length; ++j) {
        invokeDestroyHook(vnode.children[j]);
      }
    }
  }

  function removeVnodes (parentElm, vnodes, startIdx, endIdx) {
    for (; startIdx <= endIdx; ++startIdx) {
      var ch = vnodes[startIdx];
      if (isDef(ch)) {
        if (isDef(ch.tag)) {
          removeAndInvokeRemoveHook(ch);
          invokeDestroyHook(ch);
        } else { // Text node
          removeNode(ch.elm);
        }
      }
    }
  }

  function removeAndInvokeRemoveHook (vnode, rm) {
    if (isDef(rm) || isDef(vnode.data)) {
      var listeners = cbs.remove.length + 1;
      if (isDef(rm)) {
        // we have a recursively passed down rm callback
        // increase the listeners count
        rm.listeners += listeners;
      } else {
        // directly removing
        rm = createRmCb(vnode.elm, listeners);
      }
      // recursively invoke hooks on child component root node
      if (isDef(i = vnode.componentInstance) && isDef(i = i._vnode) && isDef(i.data)) {
        removeAndInvokeRemoveHook(i, rm);
      }
      for (i = 0; i < cbs.remove.length; ++i) {
        cbs.remove[i](vnode, rm);
      }
      if (isDef(i = vnode.data.hook) && isDef(i = i.remove)) {
        i(vnode, rm);
      } else {
        rm();
      }
    } else {
      removeNode(vnode.elm);
    }
  }

  function updateChildren (parentElm, oldCh, newCh, insertedVnodeQueue, removeOnly) {
    var oldStartIdx = 0;
    var newStartIdx = 0;
    var oldEndIdx = oldCh.length - 1;
    var oldStartVnode = oldCh[0];
    var oldEndVnode = oldCh[oldEndIdx];
    var newEndIdx = newCh.length - 1;
    var newStartVnode = newCh[0];
    var newEndVnode = newCh[newEndIdx];
    var oldKeyToIdx, idxInOld, elmToMove, refElm;

    // removeOnly is a special flag used only by <transition-group>
    // to ensure removed elements stay in correct relative positions
    // during leaving transitions
    var canMove = !removeOnly;

    while (oldStartIdx <= oldEndIdx && newStartIdx <= newEndIdx) {
      if (isUndef(oldStartVnode)) {
        oldStartVnode = oldCh[++oldStartIdx]; // Vnode has been moved left
      } else if (isUndef(oldEndVnode)) {
        oldEndVnode = oldCh[--oldEndIdx];
      } else if (sameVnode(oldStartVnode, newStartVnode)) {
        patchVnode(oldStartVnode, newStartVnode, insertedVnodeQueue);
        oldStartVnode = oldCh[++oldStartIdx];
        newStartVnode = newCh[++newStartIdx];
      } else if (sameVnode(oldEndVnode, newEndVnode)) {
        patchVnode(oldEndVnode, newEndVnode, insertedVnodeQueue);
        oldEndVnode = oldCh[--oldEndIdx];
        newEndVnode = newCh[--newEndIdx];
      } else if (sameVnode(oldStartVnode, newEndVnode)) { // Vnode moved right
        patchVnode(oldStartVnode, newEndVnode, insertedVnodeQueue);
        canMove && nodeOps.insertBefore(parentElm, oldStartVnode.elm, nodeOps.nextSibling(oldEndVnode.elm));
        oldStartVnode = oldCh[++oldStartIdx];
        newEndVnode = newCh[--newEndIdx];
      } else if (sameVnode(oldEndVnode, newStartVnode)) { // Vnode moved left
        patchVnode(oldEndVnode, newStartVnode, insertedVnodeQueue);
        canMove && nodeOps.insertBefore(parentElm, oldEndVnode.elm, oldStartVnode.elm);
        oldEndVnode = oldCh[--oldEndIdx];
        newStartVnode = newCh[++newStartIdx];
      } else {
        if (isUndef(oldKeyToIdx)) { oldKeyToIdx = createKeyToOldIdx(oldCh, oldStartIdx, oldEndIdx); }
        idxInOld = isDef(newStartVnode.key) ? oldKeyToIdx[newStartVnode.key] : null;
        if (isUndef(idxInOld)) { // New element
          createElm(newStartVnode, insertedVnodeQueue, parentElm, oldStartVnode.elm);
          newStartVnode = newCh[++newStartIdx];
        } else {
          elmToMove = oldCh[idxInOld];
          /* istanbul ignore if */
          if (process.env.NODE_ENV !== 'production' && !elmToMove) {
            warn(
              'It seems there are duplicate keys that is causing an update error. ' +
              'Make sure each v-for item has a unique key.'
            );
          }
          if (sameVnode(elmToMove, newStartVnode)) {
            patchVnode(elmToMove, newStartVnode, insertedVnodeQueue);
            oldCh[idxInOld] = undefined;
            canMove && nodeOps.insertBefore(parentElm, newStartVnode.elm, oldStartVnode.elm);
            newStartVnode = newCh[++newStartIdx];
          } else {
            // same key but different element. treat as new element
            createElm(newStartVnode, insertedVnodeQueue, parentElm, oldStartVnode.elm);
            newStartVnode = newCh[++newStartIdx];
          }
        }
      }
    }
    if (oldStartIdx > oldEndIdx) {
      refElm = isUndef(newCh[newEndIdx + 1]) ? null : newCh[newEndIdx + 1].elm;
      addVnodes(parentElm, refElm, newCh, newStartIdx, newEndIdx, insertedVnodeQueue);
    } else if (newStartIdx > newEndIdx) {
      removeVnodes(parentElm, oldCh, oldStartIdx, oldEndIdx);
    }
  }

  function patchVnode (oldVnode, vnode, insertedVnodeQueue, removeOnly) {
    if (oldVnode === vnode) {
      return
    }
    // reuse element for static trees.
    // note we only do this if the vnode is cloned -
    // if the new node is not cloned it means the render functions have been
    // reset by the hot-reload-api and we need to do a proper re-render.
    if (isTrue(vnode.isStatic) &&
        isTrue(oldVnode.isStatic) &&
        vnode.key === oldVnode.key &&
        (isTrue(vnode.isCloned) || isTrue(vnode.isOnce))) {
      vnode.elm = oldVnode.elm;
      vnode.componentInstance = oldVnode.componentInstance;
      return
    }
    var i;
    var data = vnode.data;
    if (isDef(data) && isDef(i = data.hook) && isDef(i = i.prepatch)) {
      i(oldVnode, vnode);
    }
    var elm = vnode.elm = oldVnode.elm;
    var oldCh = oldVnode.children;
    var ch = vnode.children;
    if (isDef(data) && isPatchable(vnode)) {
      for (i = 0; i < cbs.update.length; ++i) { cbs.update[i](oldVnode, vnode); }
      if (isDef(i = data.hook) && isDef(i = i.update)) { i(oldVnode, vnode); }
    }
    if (isUndef(vnode.text)) {
      if (isDef(oldCh) && isDef(ch)) {
        if (oldCh !== ch) { updateChildren(elm, oldCh, ch, insertedVnodeQueue, removeOnly); }
      } else if (isDef(ch)) {
        if (isDef(oldVnode.text)) { nodeOps.setTextContent(elm, ''); }
        addVnodes(elm, null, ch, 0, ch.length - 1, insertedVnodeQueue);
      } else if (isDef(oldCh)) {
        removeVnodes(elm, oldCh, 0, oldCh.length - 1);
      } else if (isDef(oldVnode.text)) {
        nodeOps.setTextContent(elm, '');
      }
    } else if (oldVnode.text !== vnode.text) {
      nodeOps.setTextContent(elm, vnode.text);
    }
    if (isDef(data)) {
      if (isDef(i = data.hook) && isDef(i = i.postpatch)) { i(oldVnode, vnode); }
    }
  }

  function invokeInsertHook (vnode, queue, initial) {
    // delay insert hooks for component root nodes, invoke them after the
    // element is really inserted
    if (isTrue(initial) && isDef(vnode.parent)) {
      vnode.parent.data.pendingInsert = queue;
    } else {
      for (var i = 0; i < queue.length; ++i) {
        queue[i].data.hook.insert(queue[i]);
      }
    }
  }

  var bailed = false;
  // list of modules that can skip create hook during hydration because they
  // are already rendered on the client or has no need for initialization
  var isRenderedModule = makeMap('attrs,style,class,staticClass,staticStyle,key');

  // Note: this is a browser-only function so we can assume elms are DOM nodes.
  function hydrate (elm, vnode, insertedVnodeQueue) {
    if (process.env.NODE_ENV !== 'production') {
      if (!assertNodeMatch(elm, vnode)) {
        return false
      }
    }
    vnode.elm = elm;
    var tag = vnode.tag;
    var data = vnode.data;
    var children = vnode.children;
    if (isDef(data)) {
      if (isDef(i = data.hook) && isDef(i = i.init)) { i(vnode, true /* hydrating */); }
      if (isDef(i = vnode.componentInstance)) {
        // child component. it should have hydrated its own tree.
        initComponent(vnode, insertedVnodeQueue);
        return true
      }
    }
    if (isDef(tag)) {
      if (isDef(children)) {
        // empty element, allow client to pick up and populate children
        if (!elm.hasChildNodes()) {
          createChildren(vnode, children, insertedVnodeQueue);
        } else {
          var childrenMatch = true;
          var childNode = elm.firstChild;
          for (var i$1 = 0; i$1 < children.length; i$1++) {
            if (!childNode || !hydrate(childNode, children[i$1], insertedVnodeQueue)) {
              childrenMatch = false;
              break
            }
            childNode = childNode.nextSibling;
          }
          // if childNode is not null, it means the actual childNodes list is
          // longer than the virtual children list.
          if (!childrenMatch || childNode) {
            if (process.env.NODE_ENV !== 'production' &&
                typeof console !== 'undefined' &&
                !bailed) {
              bailed = true;
              console.warn('Parent: ', elm);
              console.warn('Mismatching childNodes vs. VNodes: ', elm.childNodes, children);
            }
            return false
          }
        }
      }
      if (isDef(data)) {
        for (var key in data) {
          if (!isRenderedModule(key)) {
            invokeCreateHooks(vnode, insertedVnodeQueue);
            break
          }
        }
      }
    } else if (elm.data !== vnode.text) {
      elm.data = vnode.text;
    }
    return true
  }

  function assertNodeMatch (node, vnode) {
    if (isDef(vnode.tag)) {
      return (
        vnode.tag.indexOf('vue-component') === 0 ||
        vnode.tag.toLowerCase() === (node.tagName && node.tagName.toLowerCase())
      )
    } else {
      return node.nodeType === (vnode.isComment ? 8 : 3)
    }
  }

  return function patch (oldVnode, vnode, hydrating, removeOnly, parentElm, refElm) {
    if (isUndef(vnode)) {
      if (isDef(oldVnode)) { invokeDestroyHook(oldVnode); }
      return
    }

    var isInitialPatch = false;
    var insertedVnodeQueue = [];

    if (isUndef(oldVnode)) {
      // empty mount (likely as component), create new root element
      isInitialPatch = true;
      createElm(vnode, insertedVnodeQueue, parentElm, refElm);
    } else {
      var isRealElement = isDef(oldVnode.nodeType);
      if (!isRealElement && sameVnode(oldVnode, vnode)) {
        // patch existing root node
        patchVnode(oldVnode, vnode, insertedVnodeQueue, removeOnly);
      } else {
        if (isRealElement) {
          // mounting to a real element
          // check if this is server-rendered content and if we can perform
          // a successful hydration.
          if (oldVnode.nodeType === 1 && oldVnode.hasAttribute('server-rendered')) {
            oldVnode.removeAttribute('server-rendered');
            hydrating = true;
          }
          if (isTrue(hydrating)) {
            if (hydrate(oldVnode, vnode, insertedVnodeQueue)) {
              invokeInsertHook(vnode, insertedVnodeQueue, true);
              return oldVnode
            } else if (process.env.NODE_ENV !== 'production') {
              warn(
                'The client-side rendered virtual DOM tree is not matching ' +
                'server-rendered content. This is likely caused by incorrect ' +
                'HTML markup, for example nesting block-level elements inside ' +
                '<p>, or missing <tbody>. Bailing hydration and performing ' +
                'full client-side render.'
              );
            }
          }
          // either not server-rendered, or hydration failed.
          // create an empty node and replace it
          oldVnode = emptyNodeAt(oldVnode);
        }
        // replacing existing element
        var oldElm = oldVnode.elm;
        var parentElm$1 = nodeOps.parentNode(oldElm);
        createElm(
          vnode,
          insertedVnodeQueue,
          // extremely rare edge case: do not insert if old element is in a
          // leaving transition. Only happens when combining transition +
          // keep-alive + HOCs. (#4590)
          oldElm._leaveCb ? null : parentElm$1,
          nodeOps.nextSibling(oldElm)
        );

        if (isDef(vnode.parent)) {
          // component root element replaced.
          // update parent placeholder node element, recursively
          var ancestor = vnode.parent;
          while (ancestor) {
            ancestor.elm = vnode.elm;
            ancestor = ancestor.parent;
          }
          if (isPatchable(vnode)) {
            for (var i = 0; i < cbs.create.length; ++i) {
              cbs.create[i](emptyNode, vnode.parent);
            }
          }
        }

        if (isDef(parentElm$1)) {
          removeVnodes(parentElm$1, [oldVnode], 0, 0);
        } else if (isDef(oldVnode.tag)) {
          invokeDestroyHook(oldVnode);
        }
      }
    }

    invokeInsertHook(vnode, insertedVnodeQueue, isInitialPatch);
    return vnode.elm
  }
}

/*  */

var directives = {
  create: updateDirectives,
  update: updateDirectives,
  destroy: function unbindDirectives (vnode) {
    updateDirectives(vnode, emptyNode);
  }
};

function updateDirectives (oldVnode, vnode) {
  if (oldVnode.data.directives || vnode.data.directives) {
    _update(oldVnode, vnode);
  }
}

function _update (oldVnode, vnode) {
  var isCreate = oldVnode === emptyNode;
  var isDestroy = vnode === emptyNode;
  var oldDirs = normalizeDirectives$1(oldVnode.data.directives, oldVnode.context);
  var newDirs = normalizeDirectives$1(vnode.data.directives, vnode.context);

  var dirsWithInsert = [];
  var dirsWithPostpatch = [];

  var key, oldDir, dir;
  for (key in newDirs) {
    oldDir = oldDirs[key];
    dir = newDirs[key];
    if (!oldDir) {
      // new directive, bind
      callHook$1(dir, 'bind', vnode, oldVnode);
      if (dir.def && dir.def.inserted) {
        dirsWithInsert.push(dir);
      }
    } else {
      // existing directive, update
      dir.oldValue = oldDir.value;
      callHook$1(dir, 'update', vnode, oldVnode);
      if (dir.def && dir.def.componentUpdated) {
        dirsWithPostpatch.push(dir);
      }
    }
  }

  if (dirsWithInsert.length) {
    var callInsert = function () {
      for (var i = 0; i < dirsWithInsert.length; i++) {
        callHook$1(dirsWithInsert[i], 'inserted', vnode, oldVnode);
      }
    };
    if (isCreate) {
      mergeVNodeHook(vnode.data.hook || (vnode.data.hook = {}), 'insert', callInsert);
    } else {
      callInsert();
    }
  }

  if (dirsWithPostpatch.length) {
    mergeVNodeHook(vnode.data.hook || (vnode.data.hook = {}), 'postpatch', function () {
      for (var i = 0; i < dirsWithPostpatch.length; i++) {
        callHook$1(dirsWithPostpatch[i], 'componentUpdated', vnode, oldVnode);
      }
    });
  }

  if (!isCreate) {
    for (key in oldDirs) {
      if (!newDirs[key]) {
        // no longer present, unbind
        callHook$1(oldDirs[key], 'unbind', oldVnode, oldVnode, isDestroy);
      }
    }
  }
}

var emptyModifiers = Object.create(null);

function normalizeDirectives$1 (
  dirs,
  vm
) {
  var res = Object.create(null);
  if (!dirs) {
    return res
  }
  var i, dir;
  for (i = 0; i < dirs.length; i++) {
    dir = dirs[i];
    if (!dir.modifiers) {
      dir.modifiers = emptyModifiers;
    }
    res[getRawDirName(dir)] = dir;
    dir.def = resolveAsset(vm.$options, 'directives', dir.name, true);
  }
  return res
}

function getRawDirName (dir) {
  return dir.rawName || ((dir.name) + "." + (Object.keys(dir.modifiers || {}).join('.')))
}

function callHook$1 (dir, hook, vnode, oldVnode, isDestroy) {
  var fn = dir.def && dir.def[hook];
  if (fn) {
    fn(vnode.elm, dir, vnode, oldVnode, isDestroy);
  }
}

var baseModules = [
  ref,
  directives
];

/*  */

function updateAttrs (oldVnode, vnode) {
  if (!oldVnode.data.attrs && !vnode.data.attrs) {
    return
  }
  var key, cur, old;
  var elm = vnode.elm;
  var oldAttrs = oldVnode.data.attrs || {};
  var attrs = vnode.data.attrs || {};
  // clone observed objects, as the user probably wants to mutate it
  if (attrs.__ob__) {
    attrs = vnode.data.attrs = extend({}, attrs);
  }

  for (key in attrs) {
    cur = attrs[key];
    old = oldAttrs[key];
    if (old !== cur) {
      setAttr(elm, key, cur);
    }
  }
  // #4391: in IE9, setting type can reset value for input[type=radio]
  /* istanbul ignore if */
  if (isIE9 && attrs.value !== oldAttrs.value) {
    setAttr(elm, 'value', attrs.value);
  }
  for (key in oldAttrs) {
    if (attrs[key] == null) {
      if (isXlink(key)) {
        elm.removeAttributeNS(xlinkNS, getXlinkProp(key));
      } else if (!isEnumeratedAttr(key)) {
        elm.removeAttribute(key);
      }
    }
  }
}

function setAttr (el, key, value) {
  if (isBooleanAttr(key)) {
    // set attribute for blank value
    // e.g. <option disabled>Select one</option>
    if (isFalsyAttrValue(value)) {
      el.removeAttribute(key);
    } else {
      el.setAttribute(key, key);
    }
  } else if (isEnumeratedAttr(key)) {
    el.setAttribute(key, isFalsyAttrValue(value) || value === 'false' ? 'false' : 'true');
  } else if (isXlink(key)) {
    if (isFalsyAttrValue(value)) {
      el.removeAttributeNS(xlinkNS, getXlinkProp(key));
    } else {
      el.setAttributeNS(xlinkNS, key, value);
    }
  } else {
    if (isFalsyAttrValue(value)) {
      el.removeAttribute(key);
    } else {
      el.setAttribute(key, value);
    }
  }
}

var attrs = {
  create: updateAttrs,
  update: updateAttrs
};

/*  */

function updateClass (oldVnode, vnode) {
  var el = vnode.elm;
  var data = vnode.data;
  var oldData = oldVnode.data;
  if (!data.staticClass && !data.class &&
      (!oldData || (!oldData.staticClass && !oldData.class))) {
    return
  }

  var cls = genClassForVnode(vnode);

  // handle transition classes
  var transitionClass = el._transitionClasses;
  if (transitionClass) {
    cls = concat(cls, stringifyClass(transitionClass));
  }

  // set the class
  if (cls !== el._prevClass) {
    el.setAttribute('class', cls);
    el._prevClass = cls;
  }
}

var klass = {
  create: updateClass,
  update: updateClass
};

/*  */

var validDivisionCharRE = /[\w).+\-_$\]]/;



function wrapFilter (exp, filter) {
  var i = filter.indexOf('(');
  if (i < 0) {
    // _f: resolveFilter
    return ("_f(\"" + filter + "\")(" + exp + ")")
  } else {
    var name = filter.slice(0, i);
    var args = filter.slice(i + 1);
    return ("_f(\"" + name + "\")(" + exp + "," + args)
  }
}

/*  */

/*  */

/**
 * Cross-platform code generation for component v-model
 */


/**
 * Cross-platform codegen helper for generating v-model value assignment code.
 */


/**
 * parse directive model to do the array update transform. a[idx] = val => $$a.splice($$idx, 1, val)
 *
 * for loop possible cases:
 *
 * - test
 * - test[idx]
 * - test[test1[idx]]
 * - test["a"][idx]
 * - xxx.test[a[a].test1[idx]]
 * - test.xxx.a["asa"][test1[idx]]
 *
 */

var str;
var index$1;

/*  */

// in some cases, the event used has to be determined at runtime
// so we used some reserved tokens during compile.
var RANGE_TOKEN = '__r';
var CHECKBOX_RADIO_TOKEN = '__c';

/*  */

// normalize v-model event tokens that can only be determined at runtime.
// it's important to place the event as the first in the array because
// the whole point is ensuring the v-model callback gets called before
// user-attached handlers.
function normalizeEvents (on) {
  var event;
  /* istanbul ignore if */
  if (on[RANGE_TOKEN]) {
    // IE input[type=range] only supports `change` event
    event = isIE ? 'change' : 'input';
    on[event] = [].concat(on[RANGE_TOKEN], on[event] || []);
    delete on[RANGE_TOKEN];
  }
  if (on[CHECKBOX_RADIO_TOKEN]) {
    // Chrome fires microtasks in between click/change, leads to #4521
    event = isChrome ? 'click' : 'change';
    on[event] = [].concat(on[CHECKBOX_RADIO_TOKEN], on[event] || []);
    delete on[CHECKBOX_RADIO_TOKEN];
  }
}

var target$1;

function add$1 (
  event,
  handler,
  once,
  capture
) {
  if (once) {
    var oldHandler = handler;
    var _target = target$1; // save current target element in closure
    handler = function (ev) {
      var res = arguments.length === 1
        ? oldHandler(ev)
        : oldHandler.apply(null, arguments);
      if (res !== null) {
        remove$2(event, handler, capture, _target);
      }
    };
  }
  target$1.addEventListener(event, handler, capture);
}

function remove$2 (
  event,
  handler,
  capture,
  _target
) {
  (_target || target$1).removeEventListener(event, handler, capture);
}

function updateDOMListeners (oldVnode, vnode) {
  if (!oldVnode.data.on && !vnode.data.on) {
    return
  }
  var on = vnode.data.on || {};
  var oldOn = oldVnode.data.on || {};
  target$1 = vnode.elm;
  normalizeEvents(on);
  updateListeners(on, oldOn, add$1, remove$2, vnode.context);
}

var events = {
  create: updateDOMListeners,
  update: updateDOMListeners
};

/*  */

function updateDOMProps (oldVnode, vnode) {
  if (!oldVnode.data.domProps && !vnode.data.domProps) {
    return
  }
  var key, cur;
  var elm = vnode.elm;
  var oldProps = oldVnode.data.domProps || {};
  var props = vnode.data.domProps || {};
  // clone observed objects, as the user probably wants to mutate it
  if (props.__ob__) {
    props = vnode.data.domProps = extend({}, props);
  }

  for (key in oldProps) {
    if (props[key] == null) {
      elm[key] = '';
    }
  }
  for (key in props) {
    cur = props[key];
    // ignore children if the node has textContent or innerHTML,
    // as these will throw away existing DOM nodes and cause removal errors
    // on subsequent patches (#3360)
    if (key === 'textContent' || key === 'innerHTML') {
      if (vnode.children) { vnode.children.length = 0; }
      if (cur === oldProps[key]) { continue }
    }

    if (key === 'value') {
      // store value as _value as well since
      // non-string values will be stringified
      elm._value = cur;
      // avoid resetting cursor position when value is the same
      var strCur = cur == null ? '' : String(cur);
      if (shouldUpdateValue(elm, vnode, strCur)) {
        elm.value = strCur;
      }
    } else {
      elm[key] = cur;
    }
  }
}

// check platforms/web/util/attrs.js acceptValue


function shouldUpdateValue (
  elm,
  vnode,
  checkVal
) {
  return (!elm.composing && (
    vnode.tag === 'option' ||
    isDirty(elm, checkVal) ||
    isInputChanged(elm, checkVal)
  ))
}

function isDirty (elm, checkVal) {
  // return true when textbox (.number and .trim) loses focus and its value is not equal to the updated value
  return document.activeElement !== elm && elm.value !== checkVal
}

function isInputChanged (elm, newVal) {
  var value = elm.value;
  var modifiers = elm._vModifiers; // injected by v-model runtime
  if ((modifiers && modifiers.number) || elm.type === 'number') {
    return toNumber(value) !== toNumber(newVal)
  }
  if (modifiers && modifiers.trim) {
    return value.trim() !== newVal.trim()
  }
  return value !== newVal
}

var domProps = {
  create: updateDOMProps,
  update: updateDOMProps
};

/*  */

var parseStyleText = cached(function (cssText) {
  var res = {};
  var listDelimiter = /;(?![^(]*\))/g;
  var propertyDelimiter = /:(.+)/;
  cssText.split(listDelimiter).forEach(function (item) {
    if (item) {
      var tmp = item.split(propertyDelimiter);
      tmp.length > 1 && (res[tmp[0].trim()] = tmp[1].trim());
    }
  });
  return res
});

// merge static and dynamic style data on the same vnode
function normalizeStyleData (data) {
  var style = normalizeStyleBinding(data.style);
  // static style is pre-processed into an object during compilation
  // and is always a fresh object, so it's safe to merge into it
  return data.staticStyle
    ? extend(data.staticStyle, style)
    : style
}

// normalize possible array / string values into Object
function normalizeStyleBinding (bindingStyle) {
  if (Array.isArray(bindingStyle)) {
    return toObject(bindingStyle)
  }
  if (typeof bindingStyle === 'string') {
    return parseStyleText(bindingStyle)
  }
  return bindingStyle
}

/**
 * parent component style should be after child's
 * so that parent component's style could override it
 */
function getStyle (vnode, checkChild) {
  var res = {};
  var styleData;

  if (checkChild) {
    var childNode = vnode;
    while (childNode.componentInstance) {
      childNode = childNode.componentInstance._vnode;
      if (childNode.data && (styleData = normalizeStyleData(childNode.data))) {
        extend(res, styleData);
      }
    }
  }

  if ((styleData = normalizeStyleData(vnode.data))) {
    extend(res, styleData);
  }

  var parentNode = vnode;
  while ((parentNode = parentNode.parent)) {
    if (parentNode.data && (styleData = normalizeStyleData(parentNode.data))) {
      extend(res, styleData);
    }
  }
  return res
}

/*  */

var cssVarRE = /^--/;
var importantRE = /\s*!important$/;
var setProp = function (el, name, val) {
  /* istanbul ignore if */
  if (cssVarRE.test(name)) {
    el.style.setProperty(name, val);
  } else if (importantRE.test(val)) {
    el.style.setProperty(name, val.replace(importantRE, ''), 'important');
  } else {
    el.style[normalize(name)] = val;
  }
};

var prefixes = ['Webkit', 'Moz', 'ms'];

var testEl;
var normalize = cached(function (prop) {
  testEl = testEl || document.createElement('div');
  prop = camelize(prop);
  if (prop !== 'filter' && (prop in testEl.style)) {
    return prop
  }
  var upper = prop.charAt(0).toUpperCase() + prop.slice(1);
  for (var i = 0; i < prefixes.length; i++) {
    var prefixed = prefixes[i] + upper;
    if (prefixed in testEl.style) {
      return prefixed
    }
  }
});

function updateStyle (oldVnode, vnode) {
  var data = vnode.data;
  var oldData = oldVnode.data;

  if (!data.staticStyle && !data.style &&
      !oldData.staticStyle && !oldData.style) {
    return
  }

  var cur, name;
  var el = vnode.elm;
  var oldStaticStyle = oldVnode.data.staticStyle;
  var oldStyleBinding = oldVnode.data.style || {};

  // if static style exists, stylebinding already merged into it when doing normalizeStyleData
  var oldStyle = oldStaticStyle || oldStyleBinding;

  var style = normalizeStyleBinding(vnode.data.style) || {};

  vnode.data.style = style.__ob__ ? extend({}, style) : style;

  var newStyle = getStyle(vnode, true);

  for (name in oldStyle) {
    if (newStyle[name] == null) {
      setProp(el, name, '');
    }
  }
  for (name in newStyle) {
    cur = newStyle[name];
    if (cur !== oldStyle[name]) {
      // ie9 setting to null has no effect, must use empty string
      setProp(el, name, cur == null ? '' : cur);
    }
  }
}

var style = {
  create: updateStyle,
  update: updateStyle
};

/*  */

/**
 * Add class with compatibility for SVG since classList is not supported on
 * SVG elements in IE
 */
function addClass (el, cls) {
  /* istanbul ignore if */
  if (!cls || !(cls = cls.trim())) {
    return
  }

  /* istanbul ignore else */
  if (el.classList) {
    if (cls.indexOf(' ') > -1) {
      cls.split(/\s+/).forEach(function (c) { return el.classList.add(c); });
    } else {
      el.classList.add(cls);
    }
  } else {
    var cur = " " + (el.getAttribute('class') || '') + " ";
    if (cur.indexOf(' ' + cls + ' ') < 0) {
      el.setAttribute('class', (cur + cls).trim());
    }
  }
}

/**
 * Remove class with compatibility for SVG since classList is not supported on
 * SVG elements in IE
 */
function removeClass (el, cls) {
  /* istanbul ignore if */
  if (!cls || !(cls = cls.trim())) {
    return
  }

  /* istanbul ignore else */
  if (el.classList) {
    if (cls.indexOf(' ') > -1) {
      cls.split(/\s+/).forEach(function (c) { return el.classList.remove(c); });
    } else {
      el.classList.remove(cls);
    }
  } else {
    var cur = " " + (el.getAttribute('class') || '') + " ";
    var tar = ' ' + cls + ' ';
    while (cur.indexOf(tar) >= 0) {
      cur = cur.replace(tar, ' ');
    }
    el.setAttribute('class', cur.trim());
  }
}

/*  */

function resolveTransition (def$$1) {
  if (!def$$1) {
    return
  }
  /* istanbul ignore else */
  if (typeof def$$1 === 'object') {
    var res = {};
    if (def$$1.css !== false) {
      extend(res, autoCssTransition(def$$1.name || 'v'));
    }
    extend(res, def$$1);
    return res
  } else if (typeof def$$1 === 'string') {
    return autoCssTransition(def$$1)
  }
}

var autoCssTransition = cached(function (name) {
  return {
    enterClass: (name + "-enter"),
    enterToClass: (name + "-enter-to"),
    enterActiveClass: (name + "-enter-active"),
    leaveClass: (name + "-leave"),
    leaveToClass: (name + "-leave-to"),
    leaveActiveClass: (name + "-leave-active")
  }
});

var hasTransition = inBrowser && !isIE9;
var TRANSITION = 'transition';
var ANIMATION = 'animation';

// Transition property/event sniffing
var transitionProp = 'transition';
var transitionEndEvent = 'transitionend';
var animationProp = 'animation';
var animationEndEvent = 'animationend';
if (hasTransition) {
  /* istanbul ignore if */
  if (window.ontransitionend === undefined &&
    window.onwebkittransitionend !== undefined) {
    transitionProp = 'WebkitTransition';
    transitionEndEvent = 'webkitTransitionEnd';
  }
  if (window.onanimationend === undefined &&
    window.onwebkitanimationend !== undefined) {
    animationProp = 'WebkitAnimation';
    animationEndEvent = 'webkitAnimationEnd';
  }
}

// binding to window is necessary to make hot reload work in IE in strict mode
var raf = inBrowser && window.requestAnimationFrame
  ? window.requestAnimationFrame.bind(window)
  : setTimeout;

function nextFrame (fn) {
  raf(function () {
    raf(fn);
  });
}

function addTransitionClass (el, cls) {
  (el._transitionClasses || (el._transitionClasses = [])).push(cls);
  addClass(el, cls);
}

function removeTransitionClass (el, cls) {
  if (el._transitionClasses) {
    remove(el._transitionClasses, cls);
  }
  removeClass(el, cls);
}

function whenTransitionEnds (
  el,
  expectedType,
  cb
) {
  var ref = getTransitionInfo(el, expectedType);
  var type = ref.type;
  var timeout = ref.timeout;
  var propCount = ref.propCount;
  if (!type) { return cb() }
  var event = type === TRANSITION ? transitionEndEvent : animationEndEvent;
  var ended = 0;
  var end = function () {
    el.removeEventListener(event, onEnd);
    cb();
  };
  var onEnd = function (e) {
    if (e.target === el) {
      if (++ended >= propCount) {
        end();
      }
    }
  };
  setTimeout(function () {
    if (ended < propCount) {
      end();
    }
  }, timeout + 1);
  el.addEventListener(event, onEnd);
}

var transformRE = /\b(transform|all)(,|$)/;

function getTransitionInfo (el, expectedType) {
  var styles = window.getComputedStyle(el);
  var transitionDelays = styles[transitionProp + 'Delay'].split(', ');
  var transitionDurations = styles[transitionProp + 'Duration'].split(', ');
  var transitionTimeout = getTimeout(transitionDelays, transitionDurations);
  var animationDelays = styles[animationProp + 'Delay'].split(', ');
  var animationDurations = styles[animationProp + 'Duration'].split(', ');
  var animationTimeout = getTimeout(animationDelays, animationDurations);

  var type;
  var timeout = 0;
  var propCount = 0;
  /* istanbul ignore if */
  if (expectedType === TRANSITION) {
    if (transitionTimeout > 0) {
      type = TRANSITION;
      timeout = transitionTimeout;
      propCount = transitionDurations.length;
    }
  } else if (expectedType === ANIMATION) {
    if (animationTimeout > 0) {
      type = ANIMATION;
      timeout = animationTimeout;
      propCount = animationDurations.length;
    }
  } else {
    timeout = Math.max(transitionTimeout, animationTimeout);
    type = timeout > 0
      ? transitionTimeout > animationTimeout
        ? TRANSITION
        : ANIMATION
      : null;
    propCount = type
      ? type === TRANSITION
        ? transitionDurations.length
        : animationDurations.length
      : 0;
  }
  var hasTransform =
    type === TRANSITION &&
    transformRE.test(styles[transitionProp + 'Property']);
  return {
    type: type,
    timeout: timeout,
    propCount: propCount,
    hasTransform: hasTransform
  }
}

function getTimeout (delays, durations) {
  /* istanbul ignore next */
  while (delays.length < durations.length) {
    delays = delays.concat(delays);
  }

  return Math.max.apply(null, durations.map(function (d, i) {
    return toMs(d) + toMs(delays[i])
  }))
}

function toMs (s) {
  return Number(s.slice(0, -1)) * 1000
}

/*  */

function enter (vnode, toggleDisplay) {
  var el = vnode.elm;

  // call leave callback now
  if (el._leaveCb) {
    el._leaveCb.cancelled = true;
    el._leaveCb();
  }

  var data = resolveTransition(vnode.data.transition);
  if (!data) {
    return
  }

  /* istanbul ignore if */
  if (el._enterCb || el.nodeType !== 1) {
    return
  }

  var css = data.css;
  var type = data.type;
  var enterClass = data.enterClass;
  var enterToClass = data.enterToClass;
  var enterActiveClass = data.enterActiveClass;
  var appearClass = data.appearClass;
  var appearToClass = data.appearToClass;
  var appearActiveClass = data.appearActiveClass;
  var beforeEnter = data.beforeEnter;
  var enter = data.enter;
  var afterEnter = data.afterEnter;
  var enterCancelled = data.enterCancelled;
  var beforeAppear = data.beforeAppear;
  var appear = data.appear;
  var afterAppear = data.afterAppear;
  var appearCancelled = data.appearCancelled;
  var duration = data.duration;

  // activeInstance will always be the <transition> component managing this
  // transition. One edge case to check is when the <transition> is placed
  // as the root node of a child component. In that case we need to check
  // <transition>'s parent for appear check.
  var context = activeInstance;
  var transitionNode = activeInstance.$vnode;
  while (transitionNode && transitionNode.parent) {
    transitionNode = transitionNode.parent;
    context = transitionNode.context;
  }

  var isAppear = !context._isMounted || !vnode.isRootInsert;

  if (isAppear && !appear && appear !== '') {
    return
  }

  var startClass = isAppear && appearClass
    ? appearClass
    : enterClass;
  var activeClass = isAppear && appearActiveClass
    ? appearActiveClass
    : enterActiveClass;
  var toClass = isAppear && appearToClass
    ? appearToClass
    : enterToClass;

  var beforeEnterHook = isAppear
    ? (beforeAppear || beforeEnter)
    : beforeEnter;
  var enterHook = isAppear
    ? (typeof appear === 'function' ? appear : enter)
    : enter;
  var afterEnterHook = isAppear
    ? (afterAppear || afterEnter)
    : afterEnter;
  var enterCancelledHook = isAppear
    ? (appearCancelled || enterCancelled)
    : enterCancelled;

  var explicitEnterDuration = toNumber(
    isObject(duration)
      ? duration.enter
      : duration
  );

  if (process.env.NODE_ENV !== 'production' && explicitEnterDuration != null) {
    checkDuration(explicitEnterDuration, 'enter', vnode);
  }

  var expectsCSS = css !== false && !isIE9;
  var userWantsControl = getHookArgumentsLength(enterHook);

  var cb = el._enterCb = once(function () {
    if (expectsCSS) {
      removeTransitionClass(el, toClass);
      removeTransitionClass(el, activeClass);
    }
    if (cb.cancelled) {
      if (expectsCSS) {
        removeTransitionClass(el, startClass);
      }
      enterCancelledHook && enterCancelledHook(el);
    } else {
      afterEnterHook && afterEnterHook(el);
    }
    el._enterCb = null;
  });

  if (!vnode.data.show) {
    // remove pending leave element on enter by injecting an insert hook
    mergeVNodeHook(vnode.data.hook || (vnode.data.hook = {}), 'insert', function () {
      var parent = el.parentNode;
      var pendingNode = parent && parent._pending && parent._pending[vnode.key];
      if (pendingNode &&
          pendingNode.tag === vnode.tag &&
          pendingNode.elm._leaveCb) {
        pendingNode.elm._leaveCb();
      }
      enterHook && enterHook(el, cb);
    });
  }

  // start enter transition
  beforeEnterHook && beforeEnterHook(el);
  if (expectsCSS) {
    addTransitionClass(el, startClass);
    addTransitionClass(el, activeClass);
    nextFrame(function () {
      addTransitionClass(el, toClass);
      removeTransitionClass(el, startClass);
      if (!cb.cancelled && !userWantsControl) {
        if (isValidDuration(explicitEnterDuration)) {
          setTimeout(cb, explicitEnterDuration);
        } else {
          whenTransitionEnds(el, type, cb);
        }
      }
    });
  }

  if (vnode.data.show) {
    toggleDisplay && toggleDisplay();
    enterHook && enterHook(el, cb);
  }

  if (!expectsCSS && !userWantsControl) {
    cb();
  }
}

function leave (vnode, rm) {
  var el = vnode.elm;

  // call enter callback now
  if (el._enterCb) {
    el._enterCb.cancelled = true;
    el._enterCb();
  }

  var data = resolveTransition(vnode.data.transition);
  if (!data) {
    return rm()
  }

  /* istanbul ignore if */
  if (el._leaveCb || el.nodeType !== 1) {
    return
  }

  var css = data.css;
  var type = data.type;
  var leaveClass = data.leaveClass;
  var leaveToClass = data.leaveToClass;
  var leaveActiveClass = data.leaveActiveClass;
  var beforeLeave = data.beforeLeave;
  var leave = data.leave;
  var afterLeave = data.afterLeave;
  var leaveCancelled = data.leaveCancelled;
  var delayLeave = data.delayLeave;
  var duration = data.duration;

  var expectsCSS = css !== false && !isIE9;
  var userWantsControl = getHookArgumentsLength(leave);

  var explicitLeaveDuration = toNumber(
    isObject(duration)
      ? duration.leave
      : duration
  );

  if (process.env.NODE_ENV !== 'production' && explicitLeaveDuration != null) {
    checkDuration(explicitLeaveDuration, 'leave', vnode);
  }

  var cb = el._leaveCb = once(function () {
    if (el.parentNode && el.parentNode._pending) {
      el.parentNode._pending[vnode.key] = null;
    }
    if (expectsCSS) {
      removeTransitionClass(el, leaveToClass);
      removeTransitionClass(el, leaveActiveClass);
    }
    if (cb.cancelled) {
      if (expectsCSS) {
        removeTransitionClass(el, leaveClass);
      }
      leaveCancelled && leaveCancelled(el);
    } else {
      rm();
      afterLeave && afterLeave(el);
    }
    el._leaveCb = null;
  });

  if (delayLeave) {
    delayLeave(performLeave);
  } else {
    performLeave();
  }

  function performLeave () {
    // the delayed leave may have already been cancelled
    if (cb.cancelled) {
      return
    }
    // record leaving element
    if (!vnode.data.show) {
      (el.parentNode._pending || (el.parentNode._pending = {}))[vnode.key] = vnode;
    }
    beforeLeave && beforeLeave(el);
    if (expectsCSS) {
      addTransitionClass(el, leaveClass);
      addTransitionClass(el, leaveActiveClass);
      nextFrame(function () {
        addTransitionClass(el, leaveToClass);
        removeTransitionClass(el, leaveClass);
        if (!cb.cancelled && !userWantsControl) {
          if (isValidDuration(explicitLeaveDuration)) {
            setTimeout(cb, explicitLeaveDuration);
          } else {
            whenTransitionEnds(el, type, cb);
          }
        }
      });
    }
    leave && leave(el, cb);
    if (!expectsCSS && !userWantsControl) {
      cb();
    }
  }
}

// only used in dev mode
function checkDuration (val, name, vnode) {
  if (typeof val !== 'number') {
    warn(
      "<transition> explicit " + name + " duration is not a valid number - " +
      "got " + (JSON.stringify(val)) + ".",
      vnode.context
    );
  } else if (isNaN(val)) {
    warn(
      "<transition> explicit " + name + " duration is NaN - " +
      'the duration expression might be incorrect.',
      vnode.context
    );
  }
}

function isValidDuration (val) {
  return typeof val === 'number' && !isNaN(val)
}

/**
 * Normalize a transition hook's argument length. The hook may be:
 * - a merged hook (invoker) with the original in .fns
 * - a wrapped component method (check ._length)
 * - a plain function (.length)
 */
function getHookArgumentsLength (fn) {
  if (!fn) { return false }
  var invokerFns = fn.fns;
  if (invokerFns) {
    // invoker
    return getHookArgumentsLength(
      Array.isArray(invokerFns)
        ? invokerFns[0]
        : invokerFns
    )
  } else {
    return (fn._length || fn.length) > 1
  }
}

function _enter (_, vnode) {
  if (!vnode.data.show) {
    enter(vnode);
  }
}

var transition = inBrowser ? {
  create: _enter,
  activate: _enter,
  remove: function remove$$1 (vnode, rm) {
    /* istanbul ignore else */
    if (!vnode.data.show) {
      leave(vnode, rm);
    } else {
      rm();
    }
  }
} : {};

var platformModules = [
  attrs,
  klass,
  events,
  domProps,
  style,
  transition
];

/*  */

// the directive module should be applied last, after all
// built-in modules have been applied.
var modules = platformModules.concat(baseModules);

var patch = createPatchFunction({ nodeOps: nodeOps, modules: modules });

/**
 * Not type checking this file because flow doesn't like attaching
 * properties to Elements.
 */

/* istanbul ignore if */
if (isIE9) {
  // http://www.matts411.com/post/internet-explorer-9-oninput/
  document.addEventListener('selectionchange', function () {
    var el = document.activeElement;
    if (el && el.vmodel) {
      trigger(el, 'input');
    }
  });
}

var model$1 = {
  inserted: function inserted (el, binding, vnode) {
    if (vnode.tag === 'select') {
      var cb = function () {
        setSelected(el, binding, vnode.context);
      };
      cb();
      /* istanbul ignore if */
      if (isIE || isEdge) {
        setTimeout(cb, 0);
      }
    } else if (vnode.tag === 'textarea' || el.type === 'text' || el.type === 'password') {
      el._vModifiers = binding.modifiers;
      if (!binding.modifiers.lazy) {
        if (!isAndroid) {
          el.addEventListener('compositionstart', onCompositionStart);
          el.addEventListener('compositionend', onCompositionEnd);
        }
        /* istanbul ignore if */
        if (isIE9) {
          el.vmodel = true;
        }
      }
    }
  },
  componentUpdated: function componentUpdated (el, binding, vnode) {
    if (vnode.tag === 'select') {
      setSelected(el, binding, vnode.context);
      // in case the options rendered by v-for have changed,
      // it's possible that the value is out-of-sync with the rendered options.
      // detect such cases and filter out values that no longer has a matching
      // option in the DOM.
      var needReset = el.multiple
        ? binding.value.some(function (v) { return hasNoMatchingOption(v, el.options); })
        : binding.value !== binding.oldValue && hasNoMatchingOption(binding.value, el.options);
      if (needReset) {
        trigger(el, 'change');
      }
    }
  }
};

function setSelected (el, binding, vm) {
  var value = binding.value;
  var isMultiple = el.multiple;
  if (isMultiple && !Array.isArray(value)) {
    process.env.NODE_ENV !== 'production' && warn(
      "<select multiple v-model=\"" + (binding.expression) + "\"> " +
      "expects an Array value for its binding, but got " + (Object.prototype.toString.call(value).slice(8, -1)),
      vm
    );
    return
  }
  var selected, option;
  for (var i = 0, l = el.options.length; i < l; i++) {
    option = el.options[i];
    if (isMultiple) {
      selected = looseIndexOf(value, getValue(option)) > -1;
      if (option.selected !== selected) {
        option.selected = selected;
      }
    } else {
      if (looseEqual(getValue(option), value)) {
        if (el.selectedIndex !== i) {
          el.selectedIndex = i;
        }
        return
      }
    }
  }
  if (!isMultiple) {
    el.selectedIndex = -1;
  }
}

function hasNoMatchingOption (value, options) {
  for (var i = 0, l = options.length; i < l; i++) {
    if (looseEqual(getValue(options[i]), value)) {
      return false
    }
  }
  return true
}

function getValue (option) {
  return '_value' in option
    ? option._value
    : option.value
}

function onCompositionStart (e) {
  e.target.composing = true;
}

function onCompositionEnd (e) {
  e.target.composing = false;
  trigger(e.target, 'input');
}

function trigger (el, type) {
  var e = document.createEvent('HTMLEvents');
  e.initEvent(type, true, true);
  el.dispatchEvent(e);
}

/*  */

// recursively search for possible transition defined inside the component root
function locateNode (vnode) {
  return vnode.componentInstance && (!vnode.data || !vnode.data.transition)
    ? locateNode(vnode.componentInstance._vnode)
    : vnode
}

var show = {
  bind: function bind (el, ref, vnode) {
    var value = ref.value;

    vnode = locateNode(vnode);
    var transition = vnode.data && vnode.data.transition;
    var originalDisplay = el.__vOriginalDisplay =
      el.style.display === 'none' ? '' : el.style.display;
    if (value && transition && !isIE9) {
      vnode.data.show = true;
      enter(vnode, function () {
        el.style.display = originalDisplay;
      });
    } else {
      el.style.display = value ? originalDisplay : 'none';
    }
  },

  update: function update (el, ref, vnode) {
    var value = ref.value;
    var oldValue = ref.oldValue;

    /* istanbul ignore if */
    if (value === oldValue) { return }
    vnode = locateNode(vnode);
    var transition = vnode.data && vnode.data.transition;
    if (transition && !isIE9) {
      vnode.data.show = true;
      if (value) {
        enter(vnode, function () {
          el.style.display = el.__vOriginalDisplay;
        });
      } else {
        leave(vnode, function () {
          el.style.display = 'none';
        });
      }
    } else {
      el.style.display = value ? el.__vOriginalDisplay : 'none';
    }
  },

  unbind: function unbind (
    el,
    binding,
    vnode,
    oldVnode,
    isDestroy
  ) {
    if (!isDestroy) {
      el.style.display = el.__vOriginalDisplay;
    }
  }
};

var platformDirectives = {
  model: model$1,
  show: show
};

/*  */

// Provides transition support for a single element/component.
// supports transition mode (out-in / in-out)

var transitionProps = {
  name: String,
  appear: Boolean,
  css: Boolean,
  mode: String,
  type: String,
  enterClass: String,
  leaveClass: String,
  enterToClass: String,
  leaveToClass: String,
  enterActiveClass: String,
  leaveActiveClass: String,
  appearClass: String,
  appearActiveClass: String,
  appearToClass: String,
  duration: [Number, String, Object]
};

// in case the child is also an abstract component, e.g. <keep-alive>
// we want to recursively retrieve the real component to be rendered
function getRealChild (vnode) {
  var compOptions = vnode && vnode.componentOptions;
  if (compOptions && compOptions.Ctor.options.abstract) {
    return getRealChild(getFirstComponentChild(compOptions.children))
  } else {
    return vnode
  }
}

function extractTransitionData (comp) {
  var data = {};
  var options = comp.$options;
  // props
  for (var key in options.propsData) {
    data[key] = comp[key];
  }
  // events.
  // extract listeners and pass them directly to the transition methods
  var listeners = options._parentListeners;
  for (var key$1 in listeners) {
    data[camelize(key$1)] = listeners[key$1];
  }
  return data
}

function placeholder (h, rawChild) {
  return /\d-keep-alive$/.test(rawChild.tag)
    ? h('keep-alive')
    : null
}

function hasParentTransition (vnode) {
  while ((vnode = vnode.parent)) {
    if (vnode.data.transition) {
      return true
    }
  }
}

function isSameChild (child, oldChild) {
  return oldChild.key === child.key && oldChild.tag === child.tag
}

var Transition = {
  name: 'transition',
  props: transitionProps,
  abstract: true,

  render: function render (h) {
    var this$1 = this;

    var children = this.$slots.default;
    if (!children) {
      return
    }

    // filter out text nodes (possible whitespaces)
    children = children.filter(function (c) { return c.tag; });
    /* istanbul ignore if */
    if (!children.length) {
      return
    }

    // warn multiple elements
    if (process.env.NODE_ENV !== 'production' && children.length > 1) {
      warn(
        '<transition> can only be used on a single element. Use ' +
        '<transition-group> for lists.',
        this.$parent
      );
    }

    var mode = this.mode;

    // warn invalid mode
    if (process.env.NODE_ENV !== 'production' &&
        mode && mode !== 'in-out' && mode !== 'out-in') {
      warn(
        'invalid <transition> mode: ' + mode,
        this.$parent
      );
    }

    var rawChild = children[0];

    // if this is a component root node and the component's
    // parent container node also has transition, skip.
    if (hasParentTransition(this.$vnode)) {
      return rawChild
    }

    // apply transition data to child
    // use getRealChild() to ignore abstract components e.g. keep-alive
    var child = getRealChild(rawChild);
    /* istanbul ignore if */
    if (!child) {
      return rawChild
    }

    if (this._leaving) {
      return placeholder(h, rawChild)
    }

    // ensure a key that is unique to the vnode type and to this transition
    // component instance. This key will be used to remove pending leaving nodes
    // during entering.
    var id = "__transition-" + (this._uid) + "-";
    child.key = child.key == null
      ? id + child.tag
      : isPrimitive(child.key)
        ? (String(child.key).indexOf(id) === 0 ? child.key : id + child.key)
        : child.key;

    var data = (child.data || (child.data = {})).transition = extractTransitionData(this);
    var oldRawChild = this._vnode;
    var oldChild = getRealChild(oldRawChild);

    // mark v-show
    // so that the transition module can hand over the control to the directive
    if (child.data.directives && child.data.directives.some(function (d) { return d.name === 'show'; })) {
      child.data.show = true;
    }

    if (oldChild && oldChild.data && !isSameChild(child, oldChild)) {
      // replace old child transition data with fresh one
      // important for dynamic transitions!
      var oldData = oldChild && (oldChild.data.transition = extend({}, data));
      // handle transition mode
      if (mode === 'out-in') {
        // return placeholder node and queue update when leave finishes
        this._leaving = true;
        mergeVNodeHook(oldData, 'afterLeave', function () {
          this$1._leaving = false;
          this$1.$forceUpdate();
        });
        return placeholder(h, rawChild)
      } else if (mode === 'in-out') {
        var delayedLeave;
        var performLeave = function () { delayedLeave(); };
        mergeVNodeHook(data, 'afterEnter', performLeave);
        mergeVNodeHook(data, 'enterCancelled', performLeave);
        mergeVNodeHook(oldData, 'delayLeave', function (leave) { delayedLeave = leave; });
      }
    }

    return rawChild
  }
};

/*  */

// Provides transition support for list items.
// supports move transitions using the FLIP technique.

// Because the vdom's children update algorithm is "unstable" - i.e.
// it doesn't guarantee the relative positioning of removed elements,
// we force transition-group to update its children into two passes:
// in the first pass, we remove all nodes that need to be removed,
// triggering their leaving transition; in the second pass, we insert/move
// into the final desired state. This way in the second pass removed
// nodes will remain where they should be.

var props = extend({
  tag: String,
  moveClass: String
}, transitionProps);

delete props.mode;

var TransitionGroup = {
  props: props,

  render: function render (h) {
    var tag = this.tag || this.$vnode.data.tag || 'span';
    var map = Object.create(null);
    var prevChildren = this.prevChildren = this.children;
    var rawChildren = this.$slots.default || [];
    var children = this.children = [];
    var transitionData = extractTransitionData(this);

    for (var i = 0; i < rawChildren.length; i++) {
      var c = rawChildren[i];
      if (c.tag) {
        if (c.key != null && String(c.key).indexOf('__vlist') !== 0) {
          children.push(c);
          map[c.key] = c
          ;(c.data || (c.data = {})).transition = transitionData;
        } else if (process.env.NODE_ENV !== 'production') {
          var opts = c.componentOptions;
          var name = opts ? (opts.Ctor.options.name || opts.tag || '') : c.tag;
          warn(("<transition-group> children must be keyed: <" + name + ">"));
        }
      }
    }

    if (prevChildren) {
      var kept = [];
      var removed = [];
      for (var i$1 = 0; i$1 < prevChildren.length; i$1++) {
        var c$1 = prevChildren[i$1];
        c$1.data.transition = transitionData;
        c$1.data.pos = c$1.elm.getBoundingClientRect();
        if (map[c$1.key]) {
          kept.push(c$1);
        } else {
          removed.push(c$1);
        }
      }
      this.kept = h(tag, null, kept);
      this.removed = removed;
    }

    return h(tag, null, children)
  },

  beforeUpdate: function beforeUpdate () {
    // force removing pass
    this.__patch__(
      this._vnode,
      this.kept,
      false, // hydrating
      true // removeOnly (!important, avoids unnecessary moves)
    );
    this._vnode = this.kept;
  },

  updated: function updated () {
    var children = this.prevChildren;
    var moveClass = this.moveClass || ((this.name || 'v') + '-move');
    if (!children.length || !this.hasMove(children[0].elm, moveClass)) {
      return
    }

    // we divide the work into three loops to avoid mixing DOM reads and writes
    // in each iteration - which helps prevent layout thrashing.
    children.forEach(callPendingCbs);
    children.forEach(recordPosition);
    children.forEach(applyTranslation);

    // force reflow to put everything in position
    var body = document.body;
    var f = body.offsetHeight; // eslint-disable-line

    children.forEach(function (c) {
      if (c.data.moved) {
        var el = c.elm;
        var s = el.style;
        addTransitionClass(el, moveClass);
        s.transform = s.WebkitTransform = s.transitionDuration = '';
        el.addEventListener(transitionEndEvent, el._moveCb = function cb (e) {
          if (!e || /transform$/.test(e.propertyName)) {
            el.removeEventListener(transitionEndEvent, cb);
            el._moveCb = null;
            removeTransitionClass(el, moveClass);
          }
        });
      }
    });
  },

  methods: {
    hasMove: function hasMove (el, moveClass) {
      /* istanbul ignore if */
      if (!hasTransition) {
        return false
      }
      if (this._hasMove != null) {
        return this._hasMove
      }
      // Detect whether an element with the move class applied has
      // CSS transitions. Since the element may be inside an entering
      // transition at this very moment, we make a clone of it and remove
      // all other transition classes applied to ensure only the move class
      // is applied.
      var clone = el.cloneNode();
      if (el._transitionClasses) {
        el._transitionClasses.forEach(function (cls) { removeClass(clone, cls); });
      }
      addClass(clone, moveClass);
      clone.style.display = 'none';
      this.$el.appendChild(clone);
      var info = getTransitionInfo(clone);
      this.$el.removeChild(clone);
      return (this._hasMove = info.hasTransform)
    }
  }
};

function callPendingCbs (c) {
  /* istanbul ignore if */
  if (c.elm._moveCb) {
    c.elm._moveCb();
  }
  /* istanbul ignore if */
  if (c.elm._enterCb) {
    c.elm._enterCb();
  }
}

function recordPosition (c) {
  c.data.newPos = c.elm.getBoundingClientRect();
}

function applyTranslation (c) {
  var oldPos = c.data.pos;
  var newPos = c.data.newPos;
  var dx = oldPos.left - newPos.left;
  var dy = oldPos.top - newPos.top;
  if (dx || dy) {
    c.data.moved = true;
    var s = c.elm.style;
    s.transform = s.WebkitTransform = "translate(" + dx + "px," + dy + "px)";
    s.transitionDuration = '0s';
  }
}

var platformComponents = {
  Transition: Transition,
  TransitionGroup: TransitionGroup
};

/*  */

// install platform specific utils
Vue$2.config.mustUseProp = mustUseProp;
Vue$2.config.isReservedTag = isReservedTag;
Vue$2.config.getTagNamespace = getTagNamespace;
Vue$2.config.isUnknownElement = isUnknownElement;

// install platform runtime directives & components
extend(Vue$2.options.directives, platformDirectives);
extend(Vue$2.options.components, platformComponents);

// install platform patch function
Vue$2.prototype.__patch__ = inBrowser ? patch : noop;

// public mount method
Vue$2.prototype.$mount = function (
  el,
  hydrating
) {
  el = el && inBrowser ? query(el) : undefined;
  return mountComponent(this, el, hydrating)
};

// devtools global hook
/* istanbul ignore next */
setTimeout(function () {
  if (config.devtools) {
    if (devtools) {
      devtools.emit('init', Vue$2);
    } else if (process.env.NODE_ENV !== 'production' && isChrome) {
      console[console.info ? 'info' : 'log'](
        'Download the Vue Devtools extension for a better development experience:\n' +
        'https://github.com/vuejs/vue-devtools'
      );
    }
  }
  if (process.env.NODE_ENV !== 'production' &&
      config.productionTip !== false &&
      inBrowser && typeof console !== 'undefined') {
    console[console.info ? 'info' : 'log'](
      "You are running Vue in development mode.\n" +
      "Make sure to turn on production mode when deploying for production.\n" +
      "See more tips at https://vuejs.org/guide/deployment.html"
    );
  }
}, 0);

module.exports = Vue$2;

}).call(this,require('_process'),typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"_process":2}],6:[function(require,module,exports){
var inserted = exports.cache = {}

function noop () {}

exports.insert = function (css) {
  if (inserted[css]) return noop
  inserted[css] = true

  var elem = document.createElement('style')
  elem.setAttribute('type', 'text/css')

  if ('textContent' in elem) {
    elem.textContent = css
  } else {
    elem.styleSheet.cssText = css
  }

  document.getElementsByTagName('head')[0].appendChild(elem)
  return function () {
    document.getElementsByTagName('head')[0].removeChild(elem)
    inserted[css] = false
  }
}

},{}],7:[function(require,module,exports){
/**
 * vuex v2.2.1
 * (c) 2017 Evan You
 * @license MIT
 */
(function (global, factory) {
	typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
	typeof define === 'function' && define.amd ? define(factory) :
	(global.Vuex = factory());
}(this, (function () { 'use strict';

var applyMixin = function (Vue) {
  var version = Number(Vue.version.split('.')[0]);

  if (version >= 2) {
    var usesInit = Vue.config._lifecycleHooks.indexOf('init') > -1;
    Vue.mixin(usesInit ? { init: vuexInit } : { beforeCreate: vuexInit });
  } else {
    // override init and inject vuex init procedure
    // for 1.x backwards compatibility.
    var _init = Vue.prototype._init;
    Vue.prototype._init = function (options) {
      if ( options === void 0 ) options = {};

      options.init = options.init
        ? [vuexInit].concat(options.init)
        : vuexInit;
      _init.call(this, options);
    };
  }

  /**
   * Vuex init hook, injected into each instances init hooks list.
   */

  function vuexInit () {
    var options = this.$options;
    // store injection
    if (options.store) {
      this.$store = options.store;
    } else if (options.parent && options.parent.$store) {
      this.$store = options.parent.$store;
    }
  }
};

var devtoolHook =
  typeof window !== 'undefined' &&
  window.__VUE_DEVTOOLS_GLOBAL_HOOK__;

function devtoolPlugin (store) {
  if (!devtoolHook) { return }

  store._devtoolHook = devtoolHook;

  devtoolHook.emit('vuex:init', store);

  devtoolHook.on('vuex:travel-to-state', function (targetState) {
    store.replaceState(targetState);
  });

  store.subscribe(function (mutation, state) {
    devtoolHook.emit('vuex:mutation', mutation, state);
  });
}

/**
 * Get the first item that pass the test
 * by second argument function
 *
 * @param {Array} list
 * @param {Function} f
 * @return {*}
 */
/**
 * Deep copy the given object considering circular structure.
 * This function caches all nested objects and its copies.
 * If it detects circular structure, use cached copy to avoid infinite loop.
 *
 * @param {*} obj
 * @param {Array<Object>} cache
 * @return {*}
 */


/**
 * forEach for object
 */
function forEachValue (obj, fn) {
  Object.keys(obj).forEach(function (key) { return fn(obj[key], key); });
}

function isObject (obj) {
  return obj !== null && typeof obj === 'object'
}

function isPromise (val) {
  return val && typeof val.then === 'function'
}

function assert (condition, msg) {
  if (!condition) { throw new Error(("[vuex] " + msg)) }
}

var Module = function Module (rawModule, runtime) {
  this.runtime = runtime;
  this._children = Object.create(null);
  this._rawModule = rawModule;
};

var prototypeAccessors$1 = { state: {},namespaced: {} };

prototypeAccessors$1.state.get = function () {
  return this._rawModule.state || {}
};

prototypeAccessors$1.namespaced.get = function () {
  return !!this._rawModule.namespaced
};

Module.prototype.addChild = function addChild (key, module) {
  this._children[key] = module;
};

Module.prototype.removeChild = function removeChild (key) {
  delete this._children[key];
};

Module.prototype.getChild = function getChild (key) {
  return this._children[key]
};

Module.prototype.update = function update (rawModule) {
  this._rawModule.namespaced = rawModule.namespaced;
  if (rawModule.actions) {
    this._rawModule.actions = rawModule.actions;
  }
  if (rawModule.mutations) {
    this._rawModule.mutations = rawModule.mutations;
  }
  if (rawModule.getters) {
    this._rawModule.getters = rawModule.getters;
  }
};

Module.prototype.forEachChild = function forEachChild (fn) {
  forEachValue(this._children, fn);
};

Module.prototype.forEachGetter = function forEachGetter (fn) {
  if (this._rawModule.getters) {
    forEachValue(this._rawModule.getters, fn);
  }
};

Module.prototype.forEachAction = function forEachAction (fn) {
  if (this._rawModule.actions) {
    forEachValue(this._rawModule.actions, fn);
  }
};

Module.prototype.forEachMutation = function forEachMutation (fn) {
  if (this._rawModule.mutations) {
    forEachValue(this._rawModule.mutations, fn);
  }
};

Object.defineProperties( Module.prototype, prototypeAccessors$1 );

var ModuleCollection = function ModuleCollection (rawRootModule) {
  var this$1 = this;

  // register root module (Vuex.Store options)
  this.root = new Module(rawRootModule, false);

  // register all nested modules
  if (rawRootModule.modules) {
    forEachValue(rawRootModule.modules, function (rawModule, key) {
      this$1.register([key], rawModule, false);
    });
  }
};

ModuleCollection.prototype.get = function get (path) {
  return path.reduce(function (module, key) {
    return module.getChild(key)
  }, this.root)
};

ModuleCollection.prototype.getNamespace = function getNamespace (path) {
  var module = this.root;
  return path.reduce(function (namespace, key) {
    module = module.getChild(key);
    return namespace + (module.namespaced ? key + '/' : '')
  }, '')
};

ModuleCollection.prototype.update = function update$1 (rawRootModule) {
  update(this.root, rawRootModule);
};

ModuleCollection.prototype.register = function register (path, rawModule, runtime) {
    var this$1 = this;
    if ( runtime === void 0 ) runtime = true;

  var parent = this.get(path.slice(0, -1));
  var newModule = new Module(rawModule, runtime);
  parent.addChild(path[path.length - 1], newModule);

  // register nested modules
  if (rawModule.modules) {
    forEachValue(rawModule.modules, function (rawChildModule, key) {
      this$1.register(path.concat(key), rawChildModule, runtime);
    });
  }
};

ModuleCollection.prototype.unregister = function unregister (path) {
  var parent = this.get(path.slice(0, -1));
  var key = path[path.length - 1];
  if (!parent.getChild(key).runtime) { return }

  parent.removeChild(key);
};

function update (targetModule, newModule) {
  // update target module
  targetModule.update(newModule);

  // update nested modules
  if (newModule.modules) {
    for (var key in newModule.modules) {
      if (!targetModule.getChild(key)) {
        console.warn(
          "[vuex] trying to add a new module '" + key + "' on hot reloading, " +
          'manual reload is needed'
        );
        return
      }
      update(targetModule.getChild(key), newModule.modules[key]);
    }
  }
}

var Vue; // bind on install

var Store = function Store (options) {
  var this$1 = this;
  if ( options === void 0 ) options = {};

  assert(Vue, "must call Vue.use(Vuex) before creating a store instance.");
  assert(typeof Promise !== 'undefined', "vuex requires a Promise polyfill in this browser.");

  var state = options.state; if ( state === void 0 ) state = {};
  var plugins = options.plugins; if ( plugins === void 0 ) plugins = [];
  var strict = options.strict; if ( strict === void 0 ) strict = false;

  // store internal state
  this._committing = false;
  this._actions = Object.create(null);
  this._mutations = Object.create(null);
  this._wrappedGetters = Object.create(null);
  this._modules = new ModuleCollection(options);
  this._modulesNamespaceMap = Object.create(null);
  this._subscribers = [];
  this._watcherVM = new Vue();

  // bind commit and dispatch to self
  var store = this;
  var ref = this;
  var dispatch = ref.dispatch;
  var commit = ref.commit;
  this.dispatch = function boundDispatch (type, payload) {
    return dispatch.call(store, type, payload)
  };
  this.commit = function boundCommit (type, payload, options) {
    return commit.call(store, type, payload, options)
  };

  // strict mode
  this.strict = strict;

  // init root module.
  // this also recursively registers all sub-modules
  // and collects all module getters inside this._wrappedGetters
  installModule(this, state, [], this._modules.root);

  // initialize the store vm, which is responsible for the reactivity
  // (also registers _wrappedGetters as computed properties)
  resetStoreVM(this, state);

  // apply plugins
  plugins.concat(devtoolPlugin).forEach(function (plugin) { return plugin(this$1); });
};

var prototypeAccessors = { state: {} };

prototypeAccessors.state.get = function () {
  return this._vm._data.$$state
};

prototypeAccessors.state.set = function (v) {
  assert(false, "Use store.replaceState() to explicit replace store state.");
};

Store.prototype.commit = function commit (_type, _payload, _options) {
    var this$1 = this;

  // check object-style commit
  var ref = unifyObjectStyle(_type, _payload, _options);
    var type = ref.type;
    var payload = ref.payload;
    var options = ref.options;

  var mutation = { type: type, payload: payload };
  var entry = this._mutations[type];
  if (!entry) {
    console.error(("[vuex] unknown mutation type: " + type));
    return
  }
  this._withCommit(function () {
    entry.forEach(function commitIterator (handler) {
      handler(payload);
    });
  });
  this._subscribers.forEach(function (sub) { return sub(mutation, this$1.state); });

  if (options && options.silent) {
    console.warn(
      "[vuex] mutation type: " + type + ". Silent option has been removed. " +
      'Use the filter functionality in the vue-devtools'
    );
  }
};

Store.prototype.dispatch = function dispatch (_type, _payload) {
  // check object-style dispatch
  var ref = unifyObjectStyle(_type, _payload);
    var type = ref.type;
    var payload = ref.payload;

  var entry = this._actions[type];
  if (!entry) {
    console.error(("[vuex] unknown action type: " + type));
    return
  }
  return entry.length > 1
    ? Promise.all(entry.map(function (handler) { return handler(payload); }))
    : entry[0](payload)
};

Store.prototype.subscribe = function subscribe (fn) {
  var subs = this._subscribers;
  if (subs.indexOf(fn) < 0) {
    subs.push(fn);
  }
  return function () {
    var i = subs.indexOf(fn);
    if (i > -1) {
      subs.splice(i, 1);
    }
  }
};

Store.prototype.watch = function watch (getter, cb, options) {
    var this$1 = this;

  assert(typeof getter === 'function', "store.watch only accepts a function.");
  return this._watcherVM.$watch(function () { return getter(this$1.state, this$1.getters); }, cb, options)
};

Store.prototype.replaceState = function replaceState (state) {
    var this$1 = this;

  this._withCommit(function () {
    this$1._vm._data.$$state = state;
  });
};

Store.prototype.registerModule = function registerModule (path, rawModule) {
  if (typeof path === 'string') { path = [path]; }
  assert(Array.isArray(path), "module path must be a string or an Array.");
  this._modules.register(path, rawModule);
  installModule(this, this.state, path, this._modules.get(path));
  // reset store to update getters...
  resetStoreVM(this, this.state);
};

Store.prototype.unregisterModule = function unregisterModule (path) {
    var this$1 = this;

  if (typeof path === 'string') { path = [path]; }
  assert(Array.isArray(path), "module path must be a string or an Array.");
  this._modules.unregister(path);
  this._withCommit(function () {
    var parentState = getNestedState(this$1.state, path.slice(0, -1));
    Vue.delete(parentState, path[path.length - 1]);
  });
  resetStore(this);
};

Store.prototype.hotUpdate = function hotUpdate (newOptions) {
  this._modules.update(newOptions);
  resetStore(this, true);
};

Store.prototype._withCommit = function _withCommit (fn) {
  var committing = this._committing;
  this._committing = true;
  fn();
  this._committing = committing;
};

Object.defineProperties( Store.prototype, prototypeAccessors );

function resetStore (store, hot) {
  store._actions = Object.create(null);
  store._mutations = Object.create(null);
  store._wrappedGetters = Object.create(null);
  store._modulesNamespaceMap = Object.create(null);
  var state = store.state;
  // init all modules
  installModule(store, state, [], store._modules.root, true);
  // reset vm
  resetStoreVM(store, state, hot);
}

function resetStoreVM (store, state, hot) {
  var oldVm = store._vm;

  // bind store public getters
  store.getters = {};
  var wrappedGetters = store._wrappedGetters;
  var computed = {};
  forEachValue(wrappedGetters, function (fn, key) {
    // use computed to leverage its lazy-caching mechanism
    computed[key] = function () { return fn(store); };
    Object.defineProperty(store.getters, key, {
      get: function () { return store._vm[key]; },
      enumerable: true // for local getters
    });
  });

  // use a Vue instance to store the state tree
  // suppress warnings just in case the user has added
  // some funky global mixins
  var silent = Vue.config.silent;
  Vue.config.silent = true;
  store._vm = new Vue({
    data: {
      $$state: state
    },
    computed: computed
  });
  Vue.config.silent = silent;

  // enable strict mode for new vm
  if (store.strict) {
    enableStrictMode(store);
  }

  if (oldVm) {
    if (hot) {
      // dispatch changes in all subscribed watchers
      // to force getter re-evaluation for hot reloading.
      store._withCommit(function () {
        oldVm._data.$$state = null;
      });
    }
    Vue.nextTick(function () { return oldVm.$destroy(); });
  }
}

function installModule (store, rootState, path, module, hot) {
  var isRoot = !path.length;
  var namespace = store._modules.getNamespace(path);

  // register in namespace map
  if (namespace) {
    store._modulesNamespaceMap[namespace] = module;
  }

  // set state
  if (!isRoot && !hot) {
    var parentState = getNestedState(rootState, path.slice(0, -1));
    var moduleName = path[path.length - 1];
    store._withCommit(function () {
      Vue.set(parentState, moduleName, module.state);
    });
  }

  var local = module.context = makeLocalContext(store, namespace, path);

  module.forEachMutation(function (mutation, key) {
    var namespacedType = namespace + key;
    registerMutation(store, namespacedType, mutation, local);
  });

  module.forEachAction(function (action, key) {
    var namespacedType = namespace + key;
    registerAction(store, namespacedType, action, local);
  });

  module.forEachGetter(function (getter, key) {
    var namespacedType = namespace + key;
    registerGetter(store, namespacedType, getter, local);
  });

  module.forEachChild(function (child, key) {
    installModule(store, rootState, path.concat(key), child, hot);
  });
}

/**
 * make localized dispatch, commit, getters and state
 * if there is no namespace, just use root ones
 */
function makeLocalContext (store, namespace, path) {
  var noNamespace = namespace === '';

  var local = {
    dispatch: noNamespace ? store.dispatch : function (_type, _payload, _options) {
      var args = unifyObjectStyle(_type, _payload, _options);
      var payload = args.payload;
      var options = args.options;
      var type = args.type;

      if (!options || !options.root) {
        type = namespace + type;
        if (!store._actions[type]) {
          console.error(("[vuex] unknown local action type: " + (args.type) + ", global type: " + type));
          return
        }
      }

      return store.dispatch(type, payload)
    },

    commit: noNamespace ? store.commit : function (_type, _payload, _options) {
      var args = unifyObjectStyle(_type, _payload, _options);
      var payload = args.payload;
      var options = args.options;
      var type = args.type;

      if (!options || !options.root) {
        type = namespace + type;
        if (!store._mutations[type]) {
          console.error(("[vuex] unknown local mutation type: " + (args.type) + ", global type: " + type));
          return
        }
      }

      store.commit(type, payload, options);
    }
  };

  // getters and state object must be gotten lazily
  // because they will be changed by vm update
  Object.defineProperties(local, {
    getters: {
      get: noNamespace
        ? function () { return store.getters; }
        : function () { return makeLocalGetters(store, namespace); }
    },
    state: {
      get: function () { return getNestedState(store.state, path); }
    }
  });

  return local
}

function makeLocalGetters (store, namespace) {
  var gettersProxy = {};

  var splitPos = namespace.length;
  Object.keys(store.getters).forEach(function (type) {
    // skip if the target getter is not match this namespace
    if (type.slice(0, splitPos) !== namespace) { return }

    // extract local getter type
    var localType = type.slice(splitPos);

    // Add a port to the getters proxy.
    // Define as getter property because
    // we do not want to evaluate the getters in this time.
    Object.defineProperty(gettersProxy, localType, {
      get: function () { return store.getters[type]; },
      enumerable: true
    });
  });

  return gettersProxy
}

function registerMutation (store, type, handler, local) {
  var entry = store._mutations[type] || (store._mutations[type] = []);
  entry.push(function wrappedMutationHandler (payload) {
    handler(local.state, payload);
  });
}

function registerAction (store, type, handler, local) {
  var entry = store._actions[type] || (store._actions[type] = []);
  entry.push(function wrappedActionHandler (payload, cb) {
    var res = handler({
      dispatch: local.dispatch,
      commit: local.commit,
      getters: local.getters,
      state: local.state,
      rootGetters: store.getters,
      rootState: store.state
    }, payload, cb);
    if (!isPromise(res)) {
      res = Promise.resolve(res);
    }
    if (store._devtoolHook) {
      return res.catch(function (err) {
        store._devtoolHook.emit('vuex:error', err);
        throw err
      })
    } else {
      return res
    }
  });
}

function registerGetter (store, type, rawGetter, local) {
  if (store._wrappedGetters[type]) {
    console.error(("[vuex] duplicate getter key: " + type));
    return
  }
  store._wrappedGetters[type] = function wrappedGetter (store) {
    return rawGetter(
      local.state, // local state
      local.getters, // local getters
      store.state, // root state
      store.getters // root getters
    )
  };
}

function enableStrictMode (store) {
  store._vm.$watch(function () { return this._data.$$state }, function () {
    assert(store._committing, "Do not mutate vuex store state outside mutation handlers.");
  }, { deep: true, sync: true });
}

function getNestedState (state, path) {
  return path.length
    ? path.reduce(function (state, key) { return state[key]; }, state)
    : state
}

function unifyObjectStyle (type, payload, options) {
  if (isObject(type) && type.type) {
    options = payload;
    payload = type;
    type = type.type;
  }

  assert(typeof type === 'string', ("Expects string as the type, but found " + (typeof type) + "."));

  return { type: type, payload: payload, options: options }
}

function install (_Vue) {
  if (Vue) {
    console.error(
      '[vuex] already installed. Vue.use(Vuex) should be called only once.'
    );
    return
  }
  Vue = _Vue;
  applyMixin(Vue);
}

// auto install in dist mode
if (typeof window !== 'undefined' && window.Vue) {
  install(window.Vue);
}

var mapState = normalizeNamespace(function (namespace, states) {
  var res = {};
  normalizeMap(states).forEach(function (ref) {
    var key = ref.key;
    var val = ref.val;

    res[key] = function mappedState () {
      var state = this.$store.state;
      var getters = this.$store.getters;
      if (namespace) {
        var module = getModuleByNamespace(this.$store, 'mapState', namespace);
        if (!module) {
          return
        }
        state = module.context.state;
        getters = module.context.getters;
      }
      return typeof val === 'function'
        ? val.call(this, state, getters)
        : state[val]
    };
    // mark vuex getter for devtools
    res[key].vuex = true;
  });
  return res
});

var mapMutations = normalizeNamespace(function (namespace, mutations) {
  var res = {};
  normalizeMap(mutations).forEach(function (ref) {
    var key = ref.key;
    var val = ref.val;

    val = namespace + val;
    res[key] = function mappedMutation () {
      var args = [], len = arguments.length;
      while ( len-- ) args[ len ] = arguments[ len ];

      if (namespace && !getModuleByNamespace(this.$store, 'mapMutations', namespace)) {
        return
      }
      return this.$store.commit.apply(this.$store, [val].concat(args))
    };
  });
  return res
});

var mapGetters = normalizeNamespace(function (namespace, getters) {
  var res = {};
  normalizeMap(getters).forEach(function (ref) {
    var key = ref.key;
    var val = ref.val;

    val = namespace + val;
    res[key] = function mappedGetter () {
      if (namespace && !getModuleByNamespace(this.$store, 'mapGetters', namespace)) {
        return
      }
      if (!(val in this.$store.getters)) {
        console.error(("[vuex] unknown getter: " + val));
        return
      }
      return this.$store.getters[val]
    };
    // mark vuex getter for devtools
    res[key].vuex = true;
  });
  return res
});

var mapActions = normalizeNamespace(function (namespace, actions) {
  var res = {};
  normalizeMap(actions).forEach(function (ref) {
    var key = ref.key;
    var val = ref.val;

    val = namespace + val;
    res[key] = function mappedAction () {
      var args = [], len = arguments.length;
      while ( len-- ) args[ len ] = arguments[ len ];

      if (namespace && !getModuleByNamespace(this.$store, 'mapActions', namespace)) {
        return
      }
      return this.$store.dispatch.apply(this.$store, [val].concat(args))
    };
  });
  return res
});

function normalizeMap (map) {
  return Array.isArray(map)
    ? map.map(function (key) { return ({ key: key, val: key }); })
    : Object.keys(map).map(function (key) { return ({ key: key, val: map[key] }); })
}

function normalizeNamespace (fn) {
  return function (namespace, map) {
    if (typeof namespace !== 'string') {
      map = namespace;
      namespace = '';
    } else if (namespace.charAt(namespace.length - 1) !== '/') {
      namespace += '/';
    }
    return fn(namespace, map)
  }
}

function getModuleByNamespace (store, helper, namespace) {
  var module = store._modulesNamespaceMap[namespace];
  if (!module) {
    console.error(("[vuex] module namespace not found in " + helper + "(): " + namespace));
  }
  return module
}

var index = {
  Store: Store,
  install: install,
  version: '2.2.1',
  mapState: mapState,
  mapMutations: mapMutations,
  mapGetters: mapGetters,
  mapActions: mapActions
};

return index;

})));

},{}],8:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var path = require('path');

/**
 * Api class for communication with the server
 */

var Api = function () {

    /**
     * Store constructor
     */
    function Api() {
        _classCallCheck(this, Api);

        var options = Joomla.getOptions('com_media', {});
        if (options.apiBaseUrl === undefined) {
            throw new TypeError('Media api baseUrl is not defined');
        }
        if (options.csrfToken === undefined) {
            throw new TypeError('Media api csrf token is not defined');
        }

        this._baseUrl = options.apiBaseUrl;
        this._csrfToken = options.csrfToken;
    }

    /**
     * Get the contents of a directory from the server
     * @param dir
     * @returns {Promise}
     */


    _createClass(Api, [{
        key: 'getContents',
        value: function getContents(dir) {
            var _this = this;

            // Wrap the jquery call into a real promise
            return new Promise(function (resolve, reject) {
                var url = _this._baseUrl + '&task=api.files&path=' + dir;
                jQuery.getJSON(url).done(function (json) {
                    return resolve(_this._normalizeArray(json.data));
                }).fail(function (xhr, status, error) {
                    reject(xhr);
                });
            }).catch(this._handleError);
        }

        /**
         * Create a directory
         * @param name
         * @param parent
         * @returns {Promise.<T>}
         */

    }, {
        key: 'createDirectory',
        value: function createDirectory(name, parent) {
            var _this2 = this;

            // Wrap the jquery call into a real promise
            return new Promise(function (resolve, reject) {
                var _data;

                var url = _this2._baseUrl + '&task=api.files&path=' + parent;
                var data = (_data = {}, _defineProperty(_data, _this2._csrfToken, '1'), _defineProperty(_data, 'name', name), _data);
                jQuery.ajax({
                    url: url,
                    type: "POST",
                    data: JSON.stringify(data),
                    contentType: "application/json"
                }).done(function (json) {
                    return resolve(_this2._normalizeItem(json.data));
                }).fail(function (xhr, status, error) {
                    reject(xhr);
                });
            }).catch(this._handleError);
        }

        /**
         * Upload a file
         * @param name
         * @param parent
         * @param content base64 encoded string
         * @return {Promise.<T>}
         */

    }, {
        key: 'upload',
        value: function upload(name, parent, content) {
            var _this3 = this;

            // Wrap the jquery call into a real promise
            return new Promise(function (resolve, reject) {
                var _data2;

                var url = _this3._baseUrl + '&task=api.files&path=' + parent;
                var data = (_data2 = {}, _defineProperty(_data2, _this3._csrfToken, '1'), _defineProperty(_data2, 'name', name), _defineProperty(_data2, 'content', content), _data2);
                jQuery.ajax({
                    url: url,
                    type: "POST",
                    data: JSON.stringify(data),
                    contentType: "application/json"
                }).done(function (json) {
                    return resolve(_this3._normalizeItem(json.data));
                }).fail(function (xhr, status, error) {
                    reject(xhr);
                });
            }).catch(this._handleError);
        }

        /**
         * Upload a file
         * @param path
         * @return {Promise.<T>}
         */

    }, {
        key: 'delete',
        value: function _delete(path) {
            var _this4 = this;

            // Wrap the jquery call into a real promise
            return new Promise(function (resolve, reject) {
                var url = _this4._baseUrl + '&task=api.files&path=' + path;
                var data = _defineProperty({}, _this4._csrfToken, '1');
                jQuery.ajax({
                    url: url,
                    type: "DELETE",
                    data: JSON.stringify(data),
                    contentType: "application/json"
                }).done(function (json) {
                    return resolve();
                }).fail(function (xhr, status, error) {
                    reject(xhr);
                });
            }).catch(this._handleError);
        }

        /**
         * Normalize a single item
         * @param item
         * @returns {*}
         * @private
         */

    }, {
        key: '_normalizeItem',
        value: function _normalizeItem(item) {
            if (item.type === 'dir') {
                item.directories = [];
                item.files = [];
            }

            item.directory = path.dirname(item.path);

            return item;
        }

        /**
         * Normalize array data
         * @param data
         * @returns {{directories, files}}
         * @private
         */

    }, {
        key: '_normalizeArray',
        value: function _normalizeArray(data) {
            var _this5 = this;

            var directories = data.filter(function (item) {
                return item.type === 'dir';
            }).map(function (directory) {
                return _this5._normalizeItem(directory);
            });
            var files = data.filter(function (item) {
                return item.type === 'file';
            }).map(function (file) {
                return _this5._normalizeItem(file);
            });

            return {
                directories: directories,
                files: files
            };
        }

        /**
         * Handle errors
         * @param error
         * @private
         *
         * @TODO DN improve error handling
         */

    }, {
        key: '_handleError',
        value: function _handleError(error) {
            alert(error.status + ' ' + error.statusText);
            switch (error.status) {
                case 404:
                    break;
                case 401:
                case 403:
                case 500:
                    window.location.href = window.location.pathname;
                default:
                    window.location.href = window.location.pathname;
            }

            throw error;
        }
    }]);

    return Api;
}();

var api = exports.api = new Api();

},{"path":1}],9:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _vue = require("vue");

var _vue2 = _interopRequireDefault(_vue);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Media Event bus - used for communication between joomla and vue
 */
var Event = function () {

    /**
     * Media Event constructor
     */
    function Event() {
        _classCallCheck(this, Event);

        this.vue = new _vue2.default();
    }

    /**
     * Fire an event
     * @param event
     * @param data
     */


    _createClass(Event, [{
        key: "fire",
        value: function fire(event) {
            var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

            this.vue.$emit(event, data);
        }

        /**
         * Listen to events
         * @param event
         * @param callback
         */

    }, {
        key: "listen",
        value: function listen(event, callback) {
            this.vue.$on(event, callback);
        }
    }]);

    return Event;
}();

exports.default = Event;

},{"vue":5}],10:[function(require,module,exports){
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _mutationTypes = require('./../store/mutation-types');

var types = _interopRequireWildcard(_mutationTypes);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

exports.default = {
    name: 'media-app',
    data: function data() {
        return {
            fullHeight: ''
        };
    },

    methods: {
        setFullHeight: function setFullHeight() {
            this.fullHeight = window.innerHeight - this.$el.getBoundingClientRect().top + 'px';
        }
    },
    created: function created() {
        var _this = this;

        MediaManager.Event.listen('onClickCreateFolder', function () {
            return _this.$store.commit(types.SHOW_CREATE_FOLDER_MODAL);
        });
        MediaManager.Event.listen('onClickDelete', function () {
            return _this.$store.dispatch('deleteSelectedItems');
        });
    },
    mounted: function mounted() {
        var _this2 = this;

        this.$nextTick(function () {
            _this2.setFullHeight();

            window.addEventListener('resize', _this2.setFullHeight);
        });

        this.$store.dispatch('getContents', this.$store.state.selectedDirectory);
    },
    beforeDestroy: function beforeDestroy() {
        window.removeEventListener('resize', this.setFullHeight);
    }
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"media-container row",style:({minHeight: _vm.fullHeight})},[_c('div',{staticClass:"media-sidebar col-md-2 hidden-sm-down"},[_c('media-tree',{attrs:{"root":'/'}})],1),_vm._v(" "),_c('div',{staticClass:"col-md-10"},[_c('div',{staticClass:"media-main"},[_c('media-toolbar'),_vm._v(" "),_c('media-browser'),_vm._v(" "),_c('media-infobar')],1)]),_vm._v(" "),_c('media-upload'),_vm._v(" "),_c('media-create-folder-modal')],1)}
__vue__options__.staticRenderFns = []
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-1f0e8028", __vue__options__)
  } else {
    hotAPI.reload("data-v-1f0e8028", __vue__options__)
  }
})()}
},{"./../store/mutation-types":28,"vue":5,"vue-hot-reload-api":4}],11:[function(require,module,exports){
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    name: 'media-breadcrumb',
    computed: {
        crumbs: function crumbs() {
            var _this = this;

            var items = [];
            this.$store.state.selectedDirectory.split('/').filter(function (crumb) {
                return crumb.length !== 0;
            }).forEach(function (crumb) {
                items.push({
                    name: crumb,
                    path: _this.$store.state.selectedDirectory.split(crumb)[0] + crumb
                });
            });

            return items;
        },
        isLast: function isLast(item) {
            return this.crumbs.indexOf(item) === this.crumbs.length - 1;
        }
    },
    methods: {
        goTo: function goTo(path) {
            this.$store.dispatch('getContents', path);
        }
    }
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('ol',{staticClass:"media-breadcrumb breadcrumb mr-auto"},[_c('li',{staticClass:"breadcrumb-item"},[_c('a',{on:{"click":function($event){$event.stopPropagation();$event.preventDefault();_vm.goTo('/')}}},[_vm._v("Home")])]),_vm._v(" "),_vm._l((_vm.crumbs),function(crumb){return _c('li',{staticClass:"breadcrumb-item"},[_c('a',{on:{"click":function($event){$event.stopPropagation();$event.preventDefault();_vm.goTo(crumb.path)}}},[_vm._v(_vm._s(crumb.name))])])})],2)}
__vue__options__.staticRenderFns = []
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-80f631f2", __vue__options__)
  } else {
    hotAPI.reload("data-v-80f631f2", __vue__options__)
  }
})()}
},{"vue":5,"vue-hot-reload-api":4}],12:[function(require,module,exports){
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _mutationTypes = require('./../../store/mutation-types');

var types = _interopRequireWildcard(_mutationTypes);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

exports.default = {
    name: 'media-browser',
    computed: {
        items: function items() {
            var directories = this.$store.getters.getSelectedDirectoryDirectories.sort(function (a, b) {
                return a.name.toUpperCase() < b.name.toUpperCase() ? -1 : 1;
            });
            var files = this.$store.getters.getSelectedDirectoryFiles.sort(function (a, b) {
                return a.name.toUpperCase() < b.name.toUpperCase() ? -1 : 1;
            });

            return [].concat(_toConsumableArray(directories), _toConsumableArray(files));
        }
    },
    methods: {
        unselectAllBrowserItems: function unselectAllBrowserItems(event) {
            var eventOutside = !this.$refs.browserItems.contains(event.target) || event.target === this.$refs.browserItems;
            if (eventOutside) {
                this.$store.commit(types.UNSELECT_ALL_BROWSER_ITEMS);
            }
        }
    },
    created: function created() {
        document.body.addEventListener('click', this.unselectAllBrowserItems, false);
    },
    beforeDestroy: function beforeDestroy() {
        document.body.removeEventListener('click', this.unselectAllBrowserItems, false);
    }
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"media-browser"},[_c('div',{ref:"browserItems",staticClass:"media-browser-items"},_vm._l((_vm.items),function(item){return _c('media-browser-item',{attrs:{"item":item}})}))])}
__vue__options__.staticRenderFns = []
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7ea4be3f", __vue__options__)
  } else {
    hotAPI.reload("data-v-7ea4be3f", __vue__options__)
  }
})()}
},{"./../../store/mutation-types":28,"vue":5,"vue-hot-reload-api":4}],13:[function(require,module,exports){
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    name: 'media-browser-item-directory',
    props: ['item'],
    methods: {
        goTo: function goTo(path) {
            this.$store.dispatch('getContents', path);
        }
    }
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"media-browser-item-directory"},[_c('div',{staticClass:"media-browser-item-preview",on:{"dblclick":function($event){_vm.goTo(_vm.item.path)}}},[_vm._m(0)]),_vm._v(" "),_c('div',{staticClass:"media-browser-item-info"},[_vm._v("\n        "+_vm._s(_vm.item.name)+"\n    ")]),_vm._v(" "),_c('div',{staticClass:"media-browser-select"})])}
__vue__options__.staticRenderFns = [function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"file-background"},[_c('div',{staticClass:"folder-icon d-flex justify-content-center align-items-center"},[_c('span',{staticClass:"fa fa-folder-o"})])])}]
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2e7d3630", __vue__options__)
  } else {
    hotAPI.reload("data-v-2e7d3630", __vue__options__)
  }
})()}
},{"vue":5,"vue-hot-reload-api":4}],14:[function(require,module,exports){
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    name: 'media-browser-item-file',
    props: ['item']
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"media-browser-item-file",class:{selected: _vm.isSelected}},[_vm._m(0),_vm._v(" "),_c('div',{staticClass:"media-browser-item-info"},[_vm._v(_vm._s(_vm.item.name))]),_vm._v(" "),_c('div',{staticClass:"media-browser-select"})])}
__vue__options__.staticRenderFns = [function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"media-browser-item-preview"},[_c('div',{staticClass:"file-background"},[_c('div',{staticClass:"file-icon d-flex justify-content-center align-items-center"},[_c('span',{staticClass:"fa fa-file-text-o"})])])])}]
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-c8c2745e", __vue__options__)
  } else {
    hotAPI.reload("data-v-c8c2745e", __vue__options__)
  }
})()}
},{"vue":5,"vue-hot-reload-api":4}],15:[function(require,module,exports){
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    name: 'media-browser-item-image',
    props: ['item'],
    computed: {
        itemUrl: function itemUrl() {
            var fileBaseUrl = Joomla.getOptions('com_media').fileBaseUrl || '/images';

            return fileBaseUrl + this.item.path;
        }
    },
    methods: {
        deleteItem: function deleteItem() {
            this.$store.dispatch('deleteItem', this.item);
        },
        editItem: function editItem() {
            var fileBaseUrl = Joomla.getOptions('com_media').editViewUrl + '&path=';

            window.location.href = fileBaseUrl + this.item.path;
        },
        toggleSelect: function toggleSelect() {
            this.$store.dispatch('toggleBrowserItemSelect', this.item);
        }
    }
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"media-browser-image"},[_c('div',{staticClass:"media-browser-item-preview"},[_c('div',{staticClass:"image-brackground"},[_c('div',{staticClass:"image-cropped",style:({ backgroundImage: 'url(' + _vm.itemUrl + ')' }),on:{"dblclick":function($event){_vm.openEditView()}}})])]),_vm._v(" "),_c('div',{staticClass:"media-browser-item-info"},[_vm._v("\n        "+_vm._s(_vm.item.name)+" "+_vm._s(_vm.item.filetype)+"\n    ")]),_vm._v(" "),_c('div',{staticClass:"media-browser-select",on:{"click":function($event){$event.stopPropagation();_vm.toggleSelect()}}}),_vm._v(" "),_c('div',{staticClass:"media-browser-actions d-flex"},[_c('a',{staticClass:"action-delete",attrs:{"href":"#"}},[_c('span',{staticClass:"image-browser-action fa fa-trash",attrs:{"aria-hidden":"true"},on:{"click":function($event){$event.stopPropagation();_vm.deleteItem()}}})]),_vm._v(" "),_c('a',{staticClass:"action-edit",attrs:{"href":"#"}},[_c('span',{staticClass:"image-browser-action fa fa-pencil",attrs:{"aria-hidden":"true"},on:{"click":function($event){$event.stopPropagation();_vm.editItem()}}})])])])}
__vue__options__.staticRenderFns = []
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-16111b54", __vue__options__)
  } else {
    hotAPI.reload("data-v-16111b54", __vue__options__)
  }
})()}
},{"vue":5,"vue-hot-reload-api":4}],16:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _directory = require("./directory.vue");

var _directory2 = _interopRequireDefault(_directory);

var _file = require("./file.vue");

var _file2 = _interopRequireDefault(_file);

var _image = require("./image.vue");

var _image2 = _interopRequireDefault(_image);

var _mutationTypes = require("./../../../store/mutation-types");

var types = _interopRequireWildcard(_mutationTypes);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = {
    functional: true,
    props: ['item'],
    render: function render(createElement, context) {

        var store = context.parent.$store;
        var selectedItems = store.state.selectedItems;
        var item = context.props.item;

        /**
         * Return the correct item type component
         */
        function itemType() {
            var imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            // Render directory items
            if (item.type === 'dir') return _directory2.default;

            // Render image items
            if (item.extension && imageExtensions.indexOf(item.extension.toLowerCase()) !== -1) {
                return _image2.default;
            }

            // Default to file type
            return _file2.default;
        }

        /**
         * Whether or not the item is currently selected
         * @returns {boolean}
         */
        function isSelected() {
            return store.state.selectedItems.some(function (selected) {
                return selected.path === item.path;
            });
        }

        /**
         * Handle the click event
         * @param event
         */
        function handleClick(event) {
            var e = new Event('onMediaFileSelected');
            e.item = item;
            window.parent.document.dispatchEvent(e);

            // Handle clicks when the item was not selected
            if (!isSelected()) {
                // Unselect all other selected items, if the shift key was not pressed during the click event
                if (!(event.shiftKey || event.keyCode === 13)) {
                    store.commit(types.UNSELECT_ALL_BROWSER_ITEMS);
                }
                store.commit(types.SELECT_BROWSER_ITEM, item);
                return;
            }

            // If more than one item was selected and the user clicks again on the selected item,
            // he most probably wants to unselect all other items.
            if (store.state.selectedItems.length > 1) {
                store.commit(types.UNSELECT_ALL_BROWSER_ITEMS);
                store.commit(types.SELECT_BROWSER_ITEM, item);
            }
        }

        return createElement('div', {
            'class': {
                'media-browser-item': true,
                selected: isSelected()
            },
            on: {
                click: handleClick
            }
        }, [createElement(itemType(), {
            props: context.props
        })]);
    }
};

},{"./../../../store/mutation-types":28,"./directory.vue":13,"./file.vue":14,"./image.vue":15}],17:[function(require,module,exports){
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    name: 'media-infobar',
    computed: {
        item: function item() {
            var selectedItems = this.$store.state.selectedItems;

            if (selectedItems.length === 1) {
                return selectedItems[0];
            }

            if (selectedItems.length > 1) {
                return selectedItems.slice(-1)[0];
            }

            return this.$store.getters.getSelectedDirectory;
        }
    }
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"media-infobar"},[_c('span',{staticClass:"infobar-close",attrs:{"aria-label":"Close Menu"}},[_vm._v("×")]),_vm._v(" "),_c('h2',[_vm._v(_vm._s(_vm.item.name))]),_vm._v(" "),(_vm.item.path === '/')?_c('div',{staticClass:"text-center"},[_c('span',{staticClass:"fa fa-file placeholder-icon"}),_vm._v("\n        Select file or folder to view its details.\n    ")]):_c('dl',{staticClass:"row"},[_c('dt',{staticClass:"col-sm-4"},[_vm._v(_vm._s(_vm.translate('COM_MEDIA_FOLDER')))]),_vm._v(" "),_c('dd',{staticClass:"col-sm-8"},[_vm._v(_vm._s(_vm.item.directory))]),_vm._v(" "),_c('dt',{staticClass:"col-sm-4"},[_vm._v(_vm._s(_vm.translate('COM_MEDIA_MEDIA_TYPE')))]),_vm._v(" "),_c('dd',{staticClass:"col-sm-8"},[_vm._v(_vm._s(_vm.item.type || '-'))]),_vm._v(" "),_c('dt',{staticClass:"col-sm-4"},[_vm._v(_vm._s(_vm.translate('COM_MEDIA_MEDIA_CREATED_AT')))]),_vm._v(" "),_c('dd',{staticClass:"col-sm-8"},[_vm._v(_vm._s(_vm.item.create_date_formatted))]),_vm._v(" "),_c('dt',{staticClass:"col-sm-4"},[_vm._v(_vm._s(_vm.translate('COM_MEDIA_MEDIA_MODIFIED_AT')))]),_vm._v(" "),_c('dd',{staticClass:"col-sm-8"},[_vm._v(_vm._s(_vm.item.modified_date_formatted))]),_vm._v(" "),_c('dt',{staticClass:"col-sm-4"},[_vm._v(_vm._s(_vm.translate('COM_MEDIA_MEDIA_DIMENSION')))]),_vm._v(" "),(_vm.item.width || _vm.item.height)?_c('dd',{staticClass:"col-sm-8"},[_vm._v(_vm._s(_vm.item.width)+" x "+_vm._s(_vm.item.height))]):_c('dd',{staticClass:"col-sm-8"},[_vm._v("-")]),_vm._v(" "),_c('dt',{staticClass:"col-sm-4"},[_vm._v(_vm._s(_vm.translate('COM_MEDIA_MEDIA_SIZE')))]),_vm._v(" "),_c('dd',{staticClass:"col-sm-8"},[_vm._v(_vm._s(_vm.item.size || '-'))]),_vm._v(" "),_c('dt',{staticClass:"col-sm-4"},[_vm._v(_vm._s(_vm.translate('COM_MEDIA_MEDIA_MIME_TYPE')))]),_vm._v(" "),_c('dd',{staticClass:"col-sm-8"},[_vm._v(_vm._s(_vm.item.mime_type))]),_vm._v(" "),_c('dt',{staticClass:"col-sm-4"},[_vm._v(_vm._s(_vm.translate('COM_MEDIA_MEDIA_EXTENSION')))]),_vm._v(" "),_c('dd',{staticClass:"col-sm-8"},[_vm._v(_vm._s(_vm.item.extension || '-'))])])])}
__vue__options__.staticRenderFns = []
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-27fd218e", __vue__options__)
  } else {
    hotAPI.reload("data-v-27fd218e", __vue__options__)
  }
})()}
},{"vue":5,"vue-hot-reload-api":4}],18:[function(require,module,exports){
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _mutationTypes = require('./../../store/mutation-types');

var types = _interopRequireWildcard(_mutationTypes);

var _vueFocus = require('vue-focus');

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

exports.default = {
    name: 'create-folder-modal',
    directives: { focus: _vueFocus.focus },
    data: function data() {
        return {
            folder: ''
        };
    },

    methods: {
        isValid: function isValid() {
            return this.folder;
        },
        close: function close() {
            this.reset();
            this.$store.commit(types.HIDE_CREATE_FOLDER_MODAL);
        },
        save: function save() {
            if (!this.isValid()) {
                Joomla.renderMessages({ "error": [this.translate('JLIB_FORM_FIELD_REQUIRED_VALUE')] });
                return;
            }

            this.$store.dispatch('createDirectory', {
                name: this.folder,
                parent: this.$store.state.selectedDirectory
            });

            this.reset();
        },
        reset: function reset() {
            this.folder = '';
        }
    }
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return (_vm.$store.state.showCreateFolderModal)?_c('media-modal',{attrs:{"size":'sm'},on:{"close":function($event){_vm.close()}}},[_c('h3',{staticClass:"modal-title",slot:"header"},[_vm._v(_vm._s(_vm.translate('COM_MEDIA_CREATE_NEW_FOLDER')))]),_vm._v(" "),_c('div',{slot:"body"},[_c('form',{staticClass:"form",attrs:{"novalidate":""},on:{"submit":function($event){$event.preventDefault();_vm.save($event)}}},[_c('div',{staticClass:"form-group"},[_c('label',{attrs:{"for":"folder"}},[_vm._v(_vm._s(_vm.translate('COM_MEDIA_FOLDER')))]),_vm._v(" "),_c('input',{directives:[{name:"focus",rawName:"v-focus",value:(true),expression:"true"},{name:"model",rawName:"v-model.trim",value:(_vm.folder),expression:"folder",modifiers:{"trim":true}}],staticClass:"form-control",attrs:{"type":"text","id":"folder","placeholder":"Folder","required":"","autocomplete":"off"},domProps:{"value":(_vm.folder)},on:{"input":[function($event){if($event.target.composing){ return; }_vm.folder=$event.target.value.trim()},function($event){_vm.folder = $event.target.value}],"blur":function($event){_vm.$forceUpdate()}}})])])]),_vm._v(" "),_c('div',{slot:"footer"},[_c('button',{staticClass:"btn btn-link",on:{"click":function($event){_vm.close()}}},[_vm._v(_vm._s(_vm.translate('JCANCEL')))]),_vm._v(" "),_c('button',{staticClass:"btn btn-success",attrs:{"disabled":!_vm.isValid()},on:{"click":function($event){_vm.save()}}},[_vm._v(_vm._s(_vm.translate('JAPPLY')))])])]):_vm._e()}
__vue__options__.staticRenderFns = []
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-3a329564", __vue__options__)
  } else {
    hotAPI.reload("data-v-3a329564", __vue__options__)
  }
})()}
},{"./../../store/mutation-types":28,"vue":5,"vue-focus":3,"vue-hot-reload-api":4}],19:[function(require,module,exports){
var __vueify_style_dispose__ = require("vueify/lib/insert-css").insert("/** TODO DN extract styles **/\n.modal {\n    display: block;\n}\n\n.modal-body {\n    width: auto;\n    padding: 15px;\n}\n\n.media-modal-backdrop {\n    position: fixed;\n    z-index: 1040;\n    top: 0;\n    left: 0;\n    width: 100%;\n    height: 100%;\n    background-color: rgba(0, 0, 0, .5);\n    display: table;\n    transition: opacity .3s ease;\n}")
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _mutationTypes = require('./../../store/mutation-types');

var types = _interopRequireWildcard(_mutationTypes);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

exports.default = {
    name: 'media-modal',
    props: {
        showClose: {
            type: Boolean,
            default: true
        },

        size: {
            type: String
        }
    },
    computed: {
        modalClass: function modalClass() {
            return {
                'modal-sm': this.size === 'sm'
            };
        }
    },
    methods: {
        close: function close() {
            this.$emit('close');
        },
        onKeyDown: function onKeyDown(event) {
            if (event.keyCode == 27) {
                this.close();
            }
        }
    },
    mounted: function mounted() {
        document.addEventListener("keydown", this.onKeyDown);
    },
    beforeDestroy: function beforeDestroy() {
        document.removeEventListener('keydown', this.onKeyDown);
    }
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"media-modal-backdrop",on:{"click":function($event){_vm.close()}}},[_c('div',{staticClass:"modal",on:{"click":function($event){$event.stopPropagation();}}},[_c('div',{staticClass:"modal-dialog",class:_vm.modalClass,attrs:{"role":"document"}},[_c('div',{staticClass:"modal-content"},[_c('div',{staticClass:"modal-header"},[_vm._t("header"),_vm._v(" "),(_vm.showCloseButton)?_c('button',{staticClass:"close",attrs:{"type":"button","aria-label":"Close"},on:{"click":function($event){_vm.close()}}},[_c('span',{attrs:{"aria-hidden":"true"}},[_vm._v("×")])]):_vm._e()],2),_vm._v(" "),_c('div',{staticClass:"modal-body"},[_vm._t("body")],2),_vm._v(" "),_c('div',{staticClass:"modal-footer"},[_vm._t("footer")],2)])])])])}
__vue__options__.staticRenderFns = []
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  module.hot.dispose(__vueify_style_dispose__)
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-9f721108", __vue__options__)
  } else {
    hotAPI.reload("data-v-9f721108", __vue__options__)
  }
})()}
},{"./../../store/mutation-types":28,"vue":5,"vue-hot-reload-api":4,"vueify/lib/insert-css":6}],20:[function(require,module,exports){
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    name: 'media-toolbar'
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"media-toolbar"},[_c('media-breadcrumb'),_vm._v(" "),_vm._m(0)],1)}
__vue__options__.staticRenderFns = [function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"media-view-icons"},[_c('a',{staticClass:"media-toolbar-icon",attrs:{"href":"#"}},[_c('span',{staticClass:"fa fa-th",attrs:{"aria-hidden":"true"}})]),_c('a',{staticClass:"media-toolbar-icon",attrs:{"href":"#"}},[_c('span',{staticClass:"fa fa-list",attrs:{"aria-hidden":"true"}})]),_c('a',{staticClass:"media-toolbar-icon",attrs:{"href":"#"}},[_c('span',{staticClass:"fa fa-info",attrs:{"aria-hidden":"true"}})])])}]
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-110a38e5", __vue__options__)
  } else {
    hotAPI.reload("data-v-110a38e5", __vue__options__)
  }
})()}
},{"vue":5,"vue-hot-reload-api":4}],21:[function(require,module,exports){
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    name: 'media-tree-item',
    props: ['item'],
    computed: {
        isActive: function isActive() {
            return this.item.path === this.$store.state.selectedDirectory;
        },
        isOpen: function isOpen() {
            return this.$store.state.selectedDirectory.includes(this.item.path);
        },
        hasChildren: function hasChildren() {
            return this.item.directories.length > 0;
        },
        iconClass: function iconClass() {
            return {
                fa: true,
                'fa-folder-o': !this.isOpen,
                'fa-folder-open-o': this.isOpen
            };
        }
    },
    methods: {
        toggleItem: function toggleItem() {
            this.$store.dispatch('getContents', this.item.path);
        }
    }
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('li',{staticClass:"media-tree-item",class:{active: _vm.isActive}},[_c('a',{on:{"click":function($event){$event.stopPropagation();$event.preventDefault();_vm.toggleItem()}}},[_c('span',{staticClass:"item-icon"},[_c('span',{class:_vm.iconClass})]),_vm._v(" "),_c('span',{staticClass:"item-name"},[_vm._v(_vm._s(_vm.item.name))])]),_vm._v(" "),_c('transition',{attrs:{"name":"slide-fade"}},[(_vm.hasChildren)?_c('media-tree',{directives:[{name:"show",rawName:"v-show",value:(_vm.isOpen),expression:"isOpen"}],attrs:{"root":_vm.item.path}}):_vm._e()],1)],1)}
__vue__options__.staticRenderFns = []
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-0f2e6fc8", __vue__options__)
  } else {
    hotAPI.reload("data-v-0f2e6fc8", __vue__options__)
  }
})()}
},{"vue":5,"vue-hot-reload-api":4}],22:[function(require,module,exports){
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    name: 'media-tree',
    props: ['root'],
    computed: {
        directories: function directories() {
            var _this = this;

            return this.$store.state.directories.filter(function (directory) {
                return directory.directory === _this.root;
            }).sort(function (a, b) {
                return a.name.toUpperCase() < b.name.toUpperCase() ? -1 : 1;
            });
        }
    }
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('ul',{staticClass:"media-tree"},_vm._l((_vm.directories),function(item){return _c('media-tree-item',{attrs:{"item":item}})}))}
__vue__options__.staticRenderFns = []
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-04d88327", __vue__options__)
  } else {
    hotAPI.reload("data-v-04d88327", __vue__options__)
  }
})()}
},{"vue":5,"vue-hot-reload-api":4}],23:[function(require,module,exports){
;(function(){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    name: 'media-upload',
    props: {
        accept: {
            type: String
        },
        extensions: {
            default: function _default() {
                return [];
            }
        },
        name: {
            type: String,
            default: 'file'
        },
        multiple: {
            type: Boolean,
            default: true
        }
    },
    methods: {
        chooseFiles: function chooseFiles() {
            this.$refs['fileInput'].click();
        },
        upload: function upload(e) {
            var _this = this;

            e.preventDefault();
            var files = e.target.files;

            var _iteratorNormalCompletion = true;
            var _didIteratorError = false;
            var _iteratorError = undefined;

            try {
                var _loop = function _loop() {
                    var file = _step.value;

                    var reader = new FileReader();

                    reader.onload = function (progressEvent) {
                        var result = progressEvent.target.result,
                            splitIndex = result.indexOf('base64') + 7,
                            content = result.slice(splitIndex, result.length);

                        _this.$store.dispatch('uploadFile', {
                            name: file.name,
                            parent: _this.$store.state.selectedDirectory,
                            content: content
                        });
                    };

                    reader.readAsDataURL(file);
                };

                for (var _iterator = files[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                    _loop();
                }
            } catch (err) {
                _didIteratorError = true;
                _iteratorError = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion && _iterator.return) {
                        _iterator.return();
                    }
                } finally {
                    if (_didIteratorError) {
                        throw _iteratorError;
                    }
                }
            }
        }
    },
    created: function created() {
        var _this2 = this;

        MediaManager.Event.listen('onClickUpload', function () {
            return _this2.chooseFiles();
        });
    }
};
})()
if (module.exports.__esModule) module.exports = module.exports.default
var __vue__options__ = (typeof module.exports === "function"? module.exports.options: module.exports)
if (__vue__options__.functional) {console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions.")}
__vue__options__.render = function render () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('input',{ref:"fileInput",staticClass:"hidden",attrs:{"type":"file","name":_vm.name,"multiple":_vm.multiple,"accept":_vm.accept},on:{"change":_vm.upload}})}
__vue__options__.staticRenderFns = []
if (module.hot) {(function () {  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), true)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7faeb4c7", __vue__options__)
  } else {
    hotAPI.reload("data-v-7faeb4c7", __vue__options__)
  }
})()}
},{"vue":5,"vue-hot-reload-api":4}],24:[function(require,module,exports){
"use strict";

var _vue = require("vue");

var _vue2 = _interopRequireDefault(_vue);

var _Event = require("./app/Event");

var _Event2 = _interopRequireDefault(_Event);

var _app = require("./components/app.vue");

var _app2 = _interopRequireDefault(_app);

var _tree = require("./components/tree/tree.vue");

var _tree2 = _interopRequireDefault(_tree);

var _item = require("./components/tree/item.vue");

var _item2 = _interopRequireDefault(_item);

var _toolbar = require("./components/toolbar/toolbar.vue");

var _toolbar2 = _interopRequireDefault(_toolbar);

var _breadcrumb = require("./components/breadcrumb/breadcrumb.vue");

var _breadcrumb2 = _interopRequireDefault(_breadcrumb);

var _browser = require("./components/browser/browser.vue");

var _browser2 = _interopRequireDefault(_browser);

var _item3 = require("./components/browser/items/item");

var _item4 = _interopRequireDefault(_item3);

var _modal = require("./components/modals/modal.vue");

var _modal2 = _interopRequireDefault(_modal);

var _createFolderModal = require("./components/modals/create-folder-modal.vue");

var _createFolderModal2 = _interopRequireDefault(_createFolderModal);

var _infobar = require("./components/infobar/infobar.vue");

var _infobar2 = _interopRequireDefault(_infobar);

var _upload = require("./components/upload/upload.vue");

var _upload2 = _interopRequireDefault(_upload);

var _translate = require("./plugins/translate");

var _translate2 = _interopRequireDefault(_translate);

var _store = require("./store/store");

var _store2 = _interopRequireDefault(_store);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

// Add the plugins
_vue2.default.use(_translate2.default);

// Register the vue components
_vue2.default.component('media-tree', _tree2.default);
_vue2.default.component('media-tree-item', _item2.default);
_vue2.default.component('media-toolbar', _toolbar2.default);
_vue2.default.component('media-breadcrumb', _breadcrumb2.default);
_vue2.default.component('media-browser', _browser2.default);
_vue2.default.component('media-browser-item', _item4.default);
_vue2.default.component('media-modal', _modal2.default);
_vue2.default.component('media-create-folder-modal', _createFolderModal2.default);
_vue2.default.component('media-infobar', _infobar2.default);
_vue2.default.component('media-upload', _upload2.default);

// Register MediaManager namespace
window.MediaManager = window.MediaManager || {};
// Register the media manager event bus
window.MediaManager.Event = new _Event2.default();

// Create the root Vue instance
document.addEventListener("DOMContentLoaded", function (e) {
    return new _vue2.default({
        el: '#com-media',
        store: _store2.default,
        render: function render(h) {
            return h(_app2.default);
        }
    });
});

},{"./app/Event":9,"./components/app.vue":10,"./components/breadcrumb/breadcrumb.vue":11,"./components/browser/browser.vue":12,"./components/browser/items/item":16,"./components/infobar/infobar.vue":17,"./components/modals/create-folder-modal.vue":18,"./components/modals/modal.vue":19,"./components/toolbar/toolbar.vue":20,"./components/tree/item.vue":21,"./components/tree/tree.vue":22,"./components/upload/upload.vue":23,"./plugins/translate":25,"./store/store":31,"vue":5}],25:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
	value: true
});
/**
 * Translate plugin
 */

var Translate = {};

Translate.install = function (Vue, options) {
	Vue.mixin({
		methods: {
			translate: function translate(key) {
				return Joomla.JText._(key, key);
			}
		}
	});
};

exports.default = Translate;

},{}],26:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.deleteSelectedItems = exports.deleteItem = exports.uploadFile = exports.createDirectory = exports.toggleBrowserItemSelect = exports.getContents = undefined;

var _Api = require("../app/Api");

var _mutationTypes = require("./mutation-types");

var types = _interopRequireWildcard(_mutationTypes);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

// Actions are similar to mutations, the difference being that:
// - Instead of mutating the state, actions commit mutations.
// - Actions can contain arbitrary asynchronous operations.

/**
 * Get contents of a directory from the api
 * @param commit
 * @param payload
 */
var getContents = exports.getContents = function getContents(context, payload) {
    _Api.api.getContents(payload).then(function (contents) {
        context.commit(types.LOAD_CONTENTS_SUCCESS, contents);
        context.commit(types.UNSELECT_ALL_BROWSER_ITEMS);
        context.commit(types.SELECT_DIRECTORY, payload);
    }).catch(function (error) {
        // TODO error handling
        console.log("error", error);
    });
};

/**
 * Toggle the selection state of an item
 * @param commit
 * @param payload
 */
var toggleBrowserItemSelect = exports.toggleBrowserItemSelect = function toggleBrowserItemSelect(context, payload) {
    var item = payload;
    var isSelected = context.state.selectedItems.some(function (selected) {
        return selected.path === item.path;
    });
    if (!isSelected) {
        context.commit(types.SELECT_BROWSER_ITEM, item);
    } else {
        context.commit(types.UNSELECT_BROWSER_ITEM, item);
    }
};

/**
 * Create a new folder
 * @param commit
 * @param payload object with the new folder name and its parent directory
 */
var createDirectory = exports.createDirectory = function createDirectory(context, payload) {
    _Api.api.createDirectory(payload.name, payload.parent).then(function (folder) {
        context.commit(types.CREATE_DIRECTORY_SUCCESS, folder);
        context.commit(types.HIDE_CREATE_FOLDER_MODAL);
    }).catch(function (error) {
        // TODO error handling
        console.log("error", error);
    });
};

/**
 * Create a new folder
 * @param commit
 * @param payload object with the new folder name and its parent directory
 */
var uploadFile = exports.uploadFile = function uploadFile(context, payload) {
    _Api.api.upload(payload.name, payload.parent, payload.content).then(function (file) {
        context.commit(types.UPLOAD_SUCCESS, file);
    }).catch(function (error) {
        // TODO error handling
        console.log("error", error);
    });
};

/**
 * Delete a single item
 * @param context
 * @param payload object: the item to delete
 */
var deleteItem = exports.deleteItem = function deleteItem(context, payload) {
    var item = payload;
    _Api.api.delete(item.path).then(function () {
        context.commit(types.DELETE_SUCCESS, item);
        context.commit(types.UNSELECT_ALL_BROWSER_ITEMS);
    }).catch(function (error) {
        // TODO error handling
        console.log("error", error);
    });
};

/**
 * Delete the selected items
 * @param context
 * @param payload object
 */
var deleteSelectedItems = exports.deleteSelectedItems = function deleteSelectedItems(context, payload) {
    // Get the selected items from the store
    var selectedItems = context.state.selectedItems;
    if (selectedItems.length > 0) {
        selectedItems.forEach(function (item) {
            _Api.api.delete(item.path).then(function () {
                context.commit(types.DELETE_SUCCESS, item);
                context.commit(types.UNSELECT_ALL_BROWSER_ITEMS);
            }).catch(function (error) {
                // TODO error handling
                console.log("error", error);
            });
        });
    } else {
        // TODO notify the user that he has to select at least one item
    }
};

},{"../app/Api":8,"./mutation-types":28}],27:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});
// Sometimes we may need to compute derived state based on store state,
// for example filtering through a list of items and counting them.

/**
 * Get the currently selected directory
 * @param state
 * @returns {*}
 */
var getSelectedDirectory = exports.getSelectedDirectory = function getSelectedDirectory(state) {
    return state.directories.find(function (directory) {
        return directory.path === state.selectedDirectory;
    });
};

/**
 * Get the sudirectories of the currently selected directory
 * @param state
 * @param getters
 * @returns {Array|directories|{/}|computed.directories|*|Object}
 */
var getSelectedDirectoryDirectories = exports.getSelectedDirectoryDirectories = function getSelectedDirectoryDirectories(state, getters) {
    return state.directories.filter(function (directory) {
        return directory.directory === state.selectedDirectory;
    });
};

/**
 * Get the files of the currently selected directory
 * @param state
 * @param getters
 * @returns {Array|files|{}|FileList|*}
 */
var getSelectedDirectoryFiles = exports.getSelectedDirectoryFiles = function getSelectedDirectoryFiles(state, getters) {
    return state.files.filter(function (file) {
        return file.directory === state.selectedDirectory;
    });
};

},{}],28:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
var SELECT_DIRECTORY = exports.SELECT_DIRECTORY = 'SELECT_DIRECTORY';
var SELECT_BROWSER_ITEM = exports.SELECT_BROWSER_ITEM = 'SELECT_BROWSER_ITEM';
var UNSELECT_BROWSER_ITEM = exports.UNSELECT_BROWSER_ITEM = 'UNSELECT_BROWSER_ITEM';
var UNSELECT_ALL_BROWSER_ITEMS = exports.UNSELECT_ALL_BROWSER_ITEMS = 'UNSELECT_ALL_BROWSER_ITEMS';

// Api handlers
var LOAD_CONTENTS_SUCCESS = exports.LOAD_CONTENTS_SUCCESS = 'LOAD_CONTENTS_SUCCESS';
var CREATE_DIRECTORY_SUCCESS = exports.CREATE_DIRECTORY_SUCCESS = 'CREATE_DIRECTORY_SUCCESS';
var UPLOAD_SUCCESS = exports.UPLOAD_SUCCESS = 'UPLOAD_SUCCESS';

// Create folder modal
var SHOW_CREATE_FOLDER_MODAL = exports.SHOW_CREATE_FOLDER_MODAL = 'SHOW_CREATE_FOLDER_MODAL';
var HIDE_CREATE_FOLDER_MODAL = exports.HIDE_CREATE_FOLDER_MODAL = 'HIDE_CREATE_FOLDER_MODAL';

// Delete items
var DELETE_SUCCESS = exports.DELETE_SUCCESS = 'DELETE_SUCCESS';

},{}],29:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _types$SELECT_DIRECTO;

var _mutationTypes = require('./mutation-types');

var types = _interopRequireWildcard(_mutationTypes);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

// The only way to actually change state in a store is by committing a mutation.
// Mutations are very similar to events: each mutation has a string type and a handler.
// The handler function is where we perform actual state modifications, and it will receive the state as the first argument.

exports.default = (_types$SELECT_DIRECTO = {}, _defineProperty(_types$SELECT_DIRECTO, types.SELECT_DIRECTORY, function (state, payload) {
    state.selectedDirectory = payload;
}), _defineProperty(_types$SELECT_DIRECTO, types.LOAD_CONTENTS_SUCCESS, function (state, payload) {
    var newDirectories = payload.directories.filter(function (directory) {
        return !state.directories.some(function (existing) {
            return existing.path === directory.path;
        });
    });
    var newFiles = payload.files.filter(function (file) {
        return !state.files.some(function (existing) {
            return existing.path === file.path;
        });
    });

    // Merge the directories
    if (newDirectories.length > 0) {
        var _state$directories;

        var newDirectoryIds = newDirectories.map(function (directory) {
            return directory.path;
        });
        var parentDirectory = state.directories.find(function (directory) {
            return directory.path === newDirectories[0].directory;
        });
        var parentDirectoryIndex = state.directories.indexOf(parentDirectory);

        // Add the new directories to the directories array
        (_state$directories = state.directories).push.apply(_state$directories, _toConsumableArray(newDirectories));

        // Update the relation to the parent directory
        state.directories.splice(parentDirectoryIndex, 1, Object.assign({}, parentDirectory, {
            directories: [].concat(_toConsumableArray(parentDirectory.directories), _toConsumableArray(newDirectoryIds))
        }));
    }

    // Merge the files
    if (newFiles.length > 0) {
        var _state$files;

        var newFileIds = newFiles.map(function (file) {
            return file.path;
        });
        var _parentDirectory = state.directories.find(function (directory) {
            return directory.path === newFiles[0].directory;
        });
        var _parentDirectoryIndex = state.directories.indexOf(_parentDirectory);

        // Add the new files to the files array
        (_state$files = state.files).push.apply(_state$files, _toConsumableArray(newFiles));

        // Update the relation to the parent directory
        state.directories.splice(_parentDirectoryIndex, 1, Object.assign({}, _parentDirectory, {
            files: [].concat(_toConsumableArray(_parentDirectory.files), _toConsumableArray(newFileIds))
        }));
    }
}), _defineProperty(_types$SELECT_DIRECTO, types.UPLOAD_SUCCESS, function (state, payload) {
    var file = payload;
    var isNew = !state.files.some(function (existing) {
        return existing.path === file.path;
    });

    // TODO handle file_exists
    if (isNew) {
        var parentDirectory = state.directories.find(function (existing) {
            return existing.path === file.directory;
        });
        var parentDirectoryIndex = state.directories.indexOf(parentDirectory);

        // Add the new file to the files array
        state.files.push(file);

        // Update the relation to the parent directory
        state.directories.splice(parentDirectoryIndex, 1, Object.assign({}, parentDirectory, {
            files: [].concat(_toConsumableArray(parentDirectory.files), [file.path])
        }));
    }
}), _defineProperty(_types$SELECT_DIRECTO, types.CREATE_DIRECTORY_SUCCESS, function (state, payload) {

    var directory = payload;
    var isNew = !state.directories.some(function (existing) {
        return existing.path === directory.path;
    });

    if (isNew) {
        var parentDirectory = state.directories.find(function (existing) {
            return existing.path === directory.directory;
        });
        var parentDirectoryIndex = state.directories.indexOf(parentDirectory);

        // Add the new directory to the directory
        state.directories.push(directory);

        // Update the relation to the parent directory
        state.directories.splice(parentDirectoryIndex, 1, Object.assign({}, parentDirectory, {
            directories: [].concat(_toConsumableArray(parentDirectory.directories), [directory.path])
        }));
    }
}), _defineProperty(_types$SELECT_DIRECTO, types.DELETE_SUCCESS, function (state, payload) {
    var item = payload;

    // Delete file
    if (item.type === 'file') {
        state.files.splice(state.files.findIndex(function (file) {
            return file.path === item.path;
        }), 1);
    }

    // Delete dir
    if (item.type === 'dir') {
        state.directories.splice(state.directories.findIndex(function (directory) {
            return directory.path === item.path;
        }), 1);
    }
}), _defineProperty(_types$SELECT_DIRECTO, types.SELECT_BROWSER_ITEM, function (state, payload) {
    state.selectedItems.push(payload);
}), _defineProperty(_types$SELECT_DIRECTO, types.UNSELECT_BROWSER_ITEM, function (state, payload) {
    var item = payload;
    state.selectedItems.splice(state.selectedItems.findIndex(function (selectedItem) {
        return selectedItem.path === item.path;
    }), 1);
}), _defineProperty(_types$SELECT_DIRECTO, types.UNSELECT_ALL_BROWSER_ITEMS, function (state, payload) {
    state.selectedItems = [];
}), _defineProperty(_types$SELECT_DIRECTO, types.SHOW_CREATE_FOLDER_MODAL, function (state) {
    state.showCreateFolderModal = true;
}), _defineProperty(_types$SELECT_DIRECTO, types.HIDE_CREATE_FOLDER_MODAL, function (state) {
    state.showCreateFolderModal = false;
}), _types$SELECT_DIRECTO);

},{"./mutation-types":28}],30:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
// The initial state
exports.default = {
    selectedDirectory: '/',
    directories: [{ path: '/', name: 'PLACEHOLDER', directories: [], files: [], directory: null }],
    files: [],
    showCreateFolderModal: false,
    selectedItems: []
};

},{}],31:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _vue = require('vue');

var _vue2 = _interopRequireDefault(_vue);

var _vuex = require('vuex');

var _vuex2 = _interopRequireDefault(_vuex);

var _state = require('./state');

var _state2 = _interopRequireDefault(_state);

var _getters = require('./getters');

var getters = _interopRequireWildcard(_getters);

var _actions = require('./actions');

var actions = _interopRequireWildcard(_actions);

var _mutations = require('./mutations');

var _mutations2 = _interopRequireDefault(_mutations);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.use(_vuex2.default);

// A Vuex instance is created by combining the state, mutations, actions,
// and getters.
exports.default = new _vuex2.default.Store({
    state: _state2.default,
    getters: getters,
    actions: actions,
    mutations: _mutations2.default,
    strict: true
});

},{"./actions":26,"./getters":27,"./mutations":29,"./state":30,"vue":5,"vuex":7}]},{},[24]);
=======
!function e(t,n,r){function o(a,s){if(!n[a]){if(!t[a]){var c="function"==typeof require&&require;if(!s&&c)return c(a,!0);if(i)return i(a,!0);var u=new Error("Cannot find module '"+a+"'");throw u.code="MODULE_NOT_FOUND",u}var l=n[a]={exports:{}};t[a][0].call(l.exports,function(e){var n=t[a][1][e];return o(n?n:e)},l,l.exports,e,t,n,r)}return n[a].exports}for(var i="function"==typeof require&&require,a=0;a<r.length;a++)o(r[a]);return o}({1:[function(e,t,n){(function(e){function t(e,t){for(var n=0,r=e.length-1;r>=0;r--){var o=e[r];"."===o?e.splice(r,1):".."===o?(e.splice(r,1),n++):n&&(e.splice(r,1),n--)}if(t)for(;n--;n)e.unshift("..");return e}function r(e,t){if(e.filter)return e.filter(t);for(var n=[],r=0;r<e.length;r++)t(e[r],r,e)&&n.push(e[r]);return n}var o=/^(\/?|)([\s\S]*?)((?:\.{1,2}|[^\/]+?|)(\.[^.\/]*|))(?:[\/]*)$/,i=function(e){return o.exec(e).slice(1)};n.resolve=function(){for(var n="",o=!1,i=arguments.length-1;i>=-1&&!o;i--){var a=i>=0?arguments[i]:e.cwd();if("string"!=typeof a)throw new TypeError("Arguments to path.resolve must be strings");a&&(n=a+"/"+n,o="/"===a.charAt(0))}return n=t(r(n.split("/"),function(e){return!!e}),!o).join("/"),(o?"/":"")+n||"."},n.normalize=function(e){var o=n.isAbsolute(e),i="/"===a(e,-1);return e=t(r(e.split("/"),function(e){return!!e}),!o).join("/"),e||o||(e="."),e&&i&&(e+="/"),(o?"/":"")+e},n.isAbsolute=function(e){return"/"===e.charAt(0)},n.join=function(){var e=Array.prototype.slice.call(arguments,0);return n.normalize(r(e,function(e,t){if("string"!=typeof e)throw new TypeError("Arguments to path.join must be strings");return e}).join("/"))},n.relative=function(e,t){function r(e){for(var t=0;t<e.length&&""===e[t];t++);for(var n=e.length-1;n>=0&&""===e[n];n--);return t>n?[]:e.slice(t,n-t+1)}e=n.resolve(e).substr(1),t=n.resolve(t).substr(1);for(var o=r(e.split("/")),i=r(t.split("/")),a=Math.min(o.length,i.length),s=a,c=0;c<a;c++)if(o[c]!==i[c]){s=c;break}for(var u=[],c=s;c<o.length;c++)u.push("..");return u=u.concat(i.slice(s)),u.join("/")},n.sep="/",n.delimiter=":",n.dirname=function(e){var t=i(e),n=t[0],r=t[1];return n||r?(r&&(r=r.substr(0,r.length-1)),n+r):"."},n.basename=function(e,t){var n=i(e)[2];return t&&n.substr(-1*t.length)===t&&(n=n.substr(0,n.length-t.length)),n},n.extname=function(e){return i(e)[3]};var a="b"==="ab".substr(-1)?function(e,t,n){return e.substr(t,n)}:function(e,t,n){return t<0&&(t=e.length+t),e.substr(t,n)}}).call(this,e("_process"))},{_process:2}],2:[function(e,t,n){function r(){throw new Error("setTimeout has not been defined")}function o(){throw new Error("clearTimeout has not been defined")}function i(e){if(d===setTimeout)return setTimeout(e,0);if((d===r||!d)&&setTimeout)return d=setTimeout,setTimeout(e,0);try{return d(e,0)}catch(t){try{return d.call(null,e,0)}catch(t){return d.call(this,e,0)}}}function a(e){if(f===clearTimeout)return clearTimeout(e);if((f===o||!f)&&clearTimeout)return f=clearTimeout,clearTimeout(e);try{return f(e)}catch(t){try{return f.call(null,e)}catch(t){return f.call(this,e)}}}function s(){m&&v&&(m=!1,v.length?h=v.concat(h):y=-1,h.length&&c())}function c(){if(!m){var e=i(s);m=!0;for(var t=h.length;t;){for(v=h,h=[];++y<t;)v&&v[y].run();y=-1,t=h.length}v=null,m=!1,a(e)}}function u(e,t){this.fun=e,this.array=t}function l(){}var d,f,p=t.exports={};!function(){try{d="function"==typeof setTimeout?setTimeout:r}catch(e){d=r}try{f="function"==typeof clearTimeout?clearTimeout:o}catch(e){f=o}}();var v,h=[],m=!1,y=-1;p.nextTick=function(e){var t=new Array(arguments.length-1);if(arguments.length>1)for(var n=1;n<arguments.length;n++)t[n-1]=arguments[n];h.push(new u(e,t)),1!==h.length||m||i(c)},u.prototype.run=function(){this.fun.apply(null,this.array)},p.title="browser",p.browser=!0,p.env={},p.argv=[],p.version="",p.versions={},p.on=l,p.addListener=l,p.once=l,p.off=l,p.removeListener=l,p.removeAllListeners=l,p.emit=l,p.binding=function(e){throw new Error("process.binding is not supported")},p.cwd=function(){return"/"},p.chdir=function(e){throw new Error("process.chdir is not supported")},p.umask=function(){return 0}},{}],3:[function(e,t,n){function r(e,t){o(t,l,function(){s[e].instances.push(this)}),o(t,"beforeDestroy",function(){var t=s[e].instances;t.splice(t.indexOf(this),1)})}function o(e,t,n){var r=e[t];e[t]=r?Array.isArray(r)?r.concat(n):[r,n]:[n]}function i(e){return function(t,n){try{e(t,n)}catch(e){console.error(e),console.warn("Something went wrong during Vue component hot-reload. Full reload required.")}}}var a,s=window.__VUE_HOT_MAP__=Object.create(null),c=!1,u=!1,l="beforeCreate";n.install=function(e,t){if(!c)return c=!0,a=e,u=t,a.config._lifecycleHooks.indexOf("init")>-1&&(l="init"),n.compatible=Number(a.version.split(".")[0])>=2,n.compatible?void 0:void console.warn("[HMR] You are using a version of vue-hot-reload-api that is only compatible with Vue.js core ^2.0.0.")},n.createRecord=function(e,t){var n=null;"function"==typeof t&&(n=t,t=n.options),r(e,t),s[e]={Ctor:a.extend(t),instances:[]}},n.rerender=i(function(e,t){var n=s[e];n.Ctor.options.render=t.render,n.Ctor.options.staticRenderFns=t.staticRenderFns,n.instances.slice().forEach(function(e){e.$options.render=t.render,e.$options.staticRenderFns=t.staticRenderFns,e._staticTrees=[],e.$forceUpdate()})}),n.reload=i(function(e,t){r(e,t);var n=s[e];n.Ctor.extendOptions=t;var o=a.extend(t);n.Ctor.options=o.options,n.Ctor.cid=o.cid,o.release&&o.release(),n.instances.slice().forEach(function(e){e.$vnode&&e.$vnode.context?e.$vnode.context.$forceUpdate():console.warn("Root or manually mounted instance modified. Full reload required.")})})},{}],4:[function(e,t,n){(function(e,n){"use strict";function r(e){return null==e?"":"object"==typeof e?JSON.stringify(e,null,2):String(e)}function o(e){var t=parseFloat(e);return isNaN(t)?e:t}function i(e,t){for(var n=Object.create(null),r=e.split(","),o=0;o<r.length;o++)n[r[o]]=!0;return t?function(e){return n[e.toLowerCase()]}:function(e){return n[e]}}function a(e,t){if(e.length){var n=e.indexOf(t);if(n>-1)return e.splice(n,1)}}function s(e,t){return An.call(e,t)}function c(e){return"string"==typeof e||"number"==typeof e}function u(e){var t=Object.create(null);return function(n){var r=t[n];return r||(t[n]=e(n))}}function l(e,t){function n(n){var r=arguments.length;return r?r>1?e.apply(t,arguments):e.call(t,n):e.call(t)}return n._length=e.length,n}function d(e,t){t=t||0;for(var n=e.length-t,r=new Array(n);n--;)r[n]=e[n+t];return r}function f(e,t){for(var n in t)e[n]=t[n];return e}function p(e){return null!==e&&"object"==typeof e}function v(e){return Tn.call(e)===Mn}function h(e){for(var t={},n=0;n<e.length;n++)e[n]&&f(t,e[n]);return t}function m(){}function y(e){return e.reduce(function(e,t){return e.concat(t.staticKeys||[])},[]).join(",")}function _(e,t){var n=p(e),r=p(t);return n&&r?JSON.stringify(e)===JSON.stringify(t):!n&&!r&&String(e)===String(t)}function g(e,t){for(var n=0;n<e.length;n++)if(_(e[n],t))return n;return-1}function b(e){var t=(e+"").charCodeAt(0);return 36===t||95===t}function w(e,t,n,r){Object.defineProperty(e,t,{value:n,enumerable:!!r,writable:!0,configurable:!0})}function x(e){if(!Ln.test(e)){var t=e.split(".");return function(e){for(var n=0;n<t.length;n++){if(!e)return;e=e[t[n]]}return e}}}function C(e){return/native code/.test(e.toString())}function E(e){tr.target&&nr.push(tr.target),tr.target=e}function O(){tr.target=nr.pop()}function $(e,t){e.__proto__=t}function A(e,t,n){for(var r=0,o=n.length;r<o;r++){var i=n[r];w(e,i,t[i])}}function k(e,t){if(p(e)){var n;return s(e,"__ob__")&&e.__ob__ instanceof sr?n=e.__ob__:ar.shouldConvert&&!Wn()&&(Array.isArray(e)||v(e))&&Object.isExtensible(e)&&!e._isVue&&(n=new sr(e)),t&&n&&n.vmCount++,n}}function N(t,n,r,o){var i=new tr,a=Object.getOwnPropertyDescriptor(t,n);if(!a||a.configurable!==!1){var s=a&&a.get,c=a&&a.set,u=k(r);Object.defineProperty(t,n,{enumerable:!0,configurable:!0,get:function(){var e=s?s.call(t):r;return tr.target&&(i.depend(),u&&u.dep.depend(),Array.isArray(e)&&j(e)),e},set:function(n){var a=s?s.call(t):r;n===a||n!==n&&a!==a||("production"!==e.env.NODE_ENV&&o&&o(),c?c.call(t,n):r=n,u=k(n),i.notify())}})}}function S(t,n,r){if(Array.isArray(t))return t.length=Math.max(t.length,n),t.splice(n,1,r),r;if(s(t,n))return void(t[n]=r);var o=t.__ob__;return t._isVue||o&&o.vmCount?void("production"!==e.env.NODE_ENV&&Zn("Avoid adding reactive properties to a Vue instance or its root $data at runtime - declare it upfront in the data option.")):o?(N(o.value,n,r),o.dep.notify(),r):void(t[n]=r)}function D(t,n){var r=t.__ob__;return t._isVue||r&&r.vmCount?void("production"!==e.env.NODE_ENV&&Zn("Avoid deleting properties on a Vue instance or its root $data - just set it to null.")):void(s(t,n)&&(delete t[n],r&&r.dep.notify()))}function j(e){for(var t=void 0,n=0,r=e.length;n<r;n++)t=e[n],t&&t.__ob__&&t.__ob__.dep.depend(),Array.isArray(t)&&j(t)}function T(e,t){if(!t)return e;for(var n,r,o,i=Object.keys(t),a=0;a<i.length;a++)n=i[a],r=e[n],o=t[n],s(e,n)?v(r)&&v(o)&&T(r,o):S(e,n,o);return e}function M(e,t){return t?e?e.concat(t):Array.isArray(t)?t:[t]:e}function P(e,t){var n=Object.create(e||null);return t?f(n,t):n}function V(e){for(var t in e.components){var n=t.toLowerCase();($n(n)||In.isReservedTag(n))&&Zn("Do not use built-in or reserved HTML elements as component id: "+t)}}function I(t){var n=t.props;if(n){var r,o,i,a={};if(Array.isArray(n))for(r=n.length;r--;)o=n[r],"string"==typeof o?(i=Nn(o),a[i]={type:null}):"production"!==e.env.NODE_ENV&&Zn("props must be strings when using array syntax.");else if(v(n))for(var s in n)o=n[s],i=Nn(s),a[i]=v(o)?o:{type:o};t.props=a}}function L(e){var t=e.directives;if(t)for(var n in t){var r=t[n];"function"==typeof r&&(t[n]={bind:r,update:r})}}function R(t,n,r){function o(e){var o=cr[e]||lr;d[e]=o(t[e],n[e],r,e)}"production"!==e.env.NODE_ENV&&V(n),I(n),L(n);var i=n.extends;if(i&&(t="function"==typeof i?R(t,i.options,r):R(t,i,r)),n.mixins)for(var a=0,c=n.mixins.length;a<c;a++){var u=n.mixins[a];u.prototype instanceof We&&(u=u.options),t=R(t,u,r)}var l,d={};for(l in t)o(l);for(l in n)s(t,l)||o(l);return d}function U(t,n,r,o){if("string"==typeof r){var i=t[n];if(s(i,r))return i[r];var a=Nn(r);if(s(i,a))return i[a];var c=Sn(a);if(s(i,c))return i[c];var u=i[r]||i[a]||i[c];return"production"!==e.env.NODE_ENV&&o&&!u&&Zn("Failed to resolve "+n.slice(0,-1)+": "+r,t),u}}function F(t,n,r,o){var i=n[t],a=!s(r,t),c=r[t];if(G(Boolean,i.type)&&(a&&!s(i,"default")?c=!1:G(String,i.type)||""!==c&&c!==jn(t)||(c=!0)),void 0===c){c=H(o,i,t);var u=ar.shouldConvert;ar.shouldConvert=!0,k(c),ar.shouldConvert=u}return"production"!==e.env.NODE_ENV&&q(i,t,c,o,a),c}function H(t,n,r){if(s(n,"default")){var o=n.default;return p(o)&&"production"!==e.env.NODE_ENV&&Zn('Invalid default value for prop "'+r+'": Props with type Object/Array must use a factory function to return the default value.',t),t&&t.$options.propsData&&void 0===t.$options.propsData[r]&&void 0!==t[r]?t[r]:"function"==typeof o&&n.type!==Function?o.call(t):o}}function q(e,t,n,r,o){if(e.required&&o)return void Zn('Missing required prop: "'+t+'"',r);if(null!=n||e.required){var i=e.type,a=!i||i===!0,s=[];if(i){Array.isArray(i)||(i=[i]);for(var c=0;c<i.length&&!a;c++){var u=B(n,i[c]);s.push(u.expectedType||""),a=u.valid}}if(!a)return void Zn('Invalid prop: type check failed for prop "'+t+'". Expected '+s.map(Sn).join(", ")+", got "+Object.prototype.toString.call(n).slice(8,-1)+".",r);var l=e.validator;l&&(l(n)||Zn('Invalid prop: custom validator check failed for prop "'+t+'".',r))}}function B(e,t){var n,r=z(t);return n="String"===r?typeof e==(r="string"):"Number"===r?typeof e==(r="number"):"Boolean"===r?typeof e==(r="boolean"):"Function"===r?typeof e==(r="function"):"Object"===r?v(e):"Array"===r?Array.isArray(e):e instanceof t,{valid:n,expectedType:r}}function z(e){var t=e&&e.toString().match(/^\s*function (\w+)/);return t&&t[1]}function G(e,t){if(!Array.isArray(t))return z(t)===z(e);for(var n=0,r=t.length;n<r;n++)if(z(t[n])===z(e))return!0;return!1}function W(e){return new _r(void 0,void 0,void 0,String(e))}function J(e){var t=new _r(e.tag,e.data,e.children,e.text,e.elm,e.context,e.componentOptions);return t.ns=e.ns,t.isStatic=e.isStatic,t.key=e.key,t.isCloned=!0,t}function Y(e){for(var t=new Array(e.length),n=0;n<e.length;n++)t[n]=J(e[n]);return t}function K(t,n,r,o,i){if(t){var a=r.$options._base;if(p(t)&&(t=a.extend(t)),"function"!=typeof t)return void("production"!==e.env.NODE_ENV&&Zn("Invalid Component definition: "+String(t),r));if(!t.cid)if(t.resolved)t=t.resolved;else if(t=re(t,a,function(){r.$forceUpdate()}),!t)return;Ge(t),n=n||{};var s=oe(n,t);if(t.options.functional)return Z(t,s,n,r,o);var c=n.on;n.on=n.nativeOn,t.options.abstract&&(n={}),ae(n);var u=t.options.name||i,l=new _r("vue-component-"+t.cid+(u?"-"+u:""),n,void 0,void 0,void 0,r,{Ctor:t,propsData:s,listeners:c,tag:i,children:o});return l}}function Z(e,t,n,r,o){var i={},a=e.options.props;if(a)for(var s in a)i[s]=F(s,a,t);var c=Object.create(r),u=function(e,t,n,r){return he(c,e,t,n,r,!0)},l=e.options.render.call(null,u,{props:i,data:n,parent:r,children:o,slots:function(){return be(o,r)}});return l instanceof _r&&(l.functionalContext=r,n.slot&&((l.data||(l.data={})).slot=n.slot)),l}function Q(e,t,n,r){var o=e.componentOptions,i={_isComponent:!0,parent:t,propsData:o.propsData,_componentTag:o.tag,_parentVnode:e,_parentListeners:o.listeners,_renderChildren:o.children,_parentElm:n||null,_refElm:r||null},a=e.data.inlineTemplate;return a&&(i.render=a.render,i.staticRenderFns=a.staticRenderFns),new o.Ctor(i)}function X(e,t,n,r){if(!e.componentInstance||e.componentInstance._isDestroyed){var o=e.componentInstance=Q(e,Ar,n,r);o.$mount(t?e.elm:void 0,t)}else if(e.data.keepAlive){var i=e;ee(i,i)}}function ee(e,t){var n=t.componentOptions,r=t.componentInstance=e.componentInstance;r._updateFromParent(n.propsData,n.listeners,t,n.children)}function te(e){e.componentInstance._isMounted||(e.componentInstance._isMounted=!0,ke(e.componentInstance,"mounted")),e.data.keepAlive&&(e.componentInstance._inactive=!1,ke(e.componentInstance,"activated"))}function ne(e){e.componentInstance._isDestroyed||(e.data.keepAlive?(e.componentInstance._inactive=!0,ke(e.componentInstance,"deactivated")):e.componentInstance.$destroy())}function re(t,n,r){if(!t.requested){t.requested=!0;var o=t.pendingCallbacks=[r],i=!0,a=function(e){if(p(e)&&(e=n.extend(e)),t.resolved=e,!i)for(var r=0,a=o.length;r<a;r++)o[r](e)},s=function(n){"production"!==e.env.NODE_ENV&&Zn("Failed to resolve async component: "+String(t)+(n?"\nReason: "+n:""))},c=t(a,s);return c&&"function"==typeof c.then&&!t.resolved&&c.then(a,s),i=!1,t.resolved}t.pendingCallbacks.push(r)}function oe(e,t){var n=t.options.props;if(n){var r={},o=e.attrs,i=e.props,a=e.domProps;if(o||i||a)for(var s in n){var c=jn(s);ie(r,i,s,c,!0)||ie(r,o,s,c)||ie(r,a,s,c)}return r}}function ie(e,t,n,r,o){if(t){if(s(t,n))return e[n]=t[n],o||delete t[n],!0;if(s(t,r))return e[n]=t[r],o||delete t[r],!0}return!1}function ae(e){e.hook||(e.hook={});for(var t=0;t<Cr.length;t++){var n=Cr[t],r=e.hook[n],o=xr[n];e.hook[n]=r?se(o,r):o}}function se(e,t){return function(n,r,o,i){e(n,r,o,i),t(n,r,o,i)}}function ce(e,t,n,r){r+=t;var o=e.__injected||(e.__injected={});if(!o[r]){o[r]=!0;var i=e[t];i?e[t]=function(){i.apply(this,arguments),n.apply(this,arguments)}:e[t]=n}}function ue(e){var t={fn:e,invoker:function(){var e=arguments,n=t.fn;if(Array.isArray(n))for(var r=0;r<n.length;r++)n[r].apply(null,e);else n.apply(null,arguments)}};return t}function le(t,n,r,o,i){var a,s,c,u;for(a in t)s=t[a],c=n[a],u=Er(a),s?c?s!==c&&(c.fn=s,t[a]=c):(s.invoker||(s=t[a]=ue(s)),r(u.name,s.invoker,u.once,u.capture)):"production"!==e.env.NODE_ENV&&Zn('Invalid handler for event "'+u.name+'": got '+String(s),i);for(a in n)t[a]||(u=Er(a),o(u.name,n[a].invoker,u.capture))}function de(e){for(var t=0;t<e.length;t++)if(Array.isArray(e[t]))return Array.prototype.concat.apply([],e);return e}function fe(e){return c(e)?[W(e)]:Array.isArray(e)?pe(e):void 0}function pe(e,t){var n,r,o,i=[];for(n=0;n<e.length;n++)r=e[n],null!=r&&"boolean"!=typeof r&&(o=i[i.length-1],Array.isArray(r)?i.push.apply(i,pe(r,(t||"")+"_"+n)):c(r)?o&&o.text?o.text+=String(r):""!==r&&i.push(W(r)):r.text&&o&&o.text?i[i.length-1]=W(o.text+r.text):(r.tag&&null==r.key&&null!=t&&(r.key="__vlist"+t+"_"+n+"__"),i.push(r)));return i}function ve(e){return e&&e.filter(function(e){return e&&e.componentOptions})[0]}function he(e,t,n,r,o,i){return(Array.isArray(n)||c(n))&&(o=r,r=n,n=void 0),i&&(o=$r),me(e,t,n,r,o)}function me(t,n,r,o,i){if(r&&r.__ob__)return"production"!==e.env.NODE_ENV&&Zn("Avoid using observed data object as vnode data: "+JSON.stringify(r)+"\nAlways create fresh vnode data objects in each render!",t),wr();if(!n)return wr();Array.isArray(o)&&"function"==typeof o[0]&&(r=r||{},r.scopedSlots={default:o[0]},o.length=0),i===$r?o=fe(o):i===Or&&(o=de(o));var a,s;if("string"==typeof n){var c;s=In.getTagNamespace(n),a=In.isReservedTag(n)?new _r(In.parsePlatformTagName(n),r,o,void 0,void 0,t):(c=U(t.$options,"components",n))?K(c,r,t,o,n):new _r(n,r,o,void 0,void 0,t)}else a=K(n,r,t,o);return a?(s&&ye(a,s),a):wr()}function ye(e,t){if(e.ns=t,"foreignObject"!==e.tag&&e.children)for(var n=0,r=e.children.length;n<r;n++){var o=e.children[n];o.tag&&!o.ns&&ye(o,t)}}function _e(e){e.$vnode=null,e._vnode=null,e._staticTrees=null;var t=e.$options._parentVnode,n=t&&t.context;e.$slots=be(e.$options._renderChildren,n),e.$scopedSlots={},e._c=function(t,n,r,o){return he(e,t,n,r,o,!1)},e.$createElement=function(t,n,r,o){return he(e,t,n,r,o,!0)}}function ge(t){function n(e,t,n){if(Array.isArray(e))for(var r=0;r<e.length;r++)e[r]&&"string"!=typeof e[r]&&i(e[r],t+"_"+r,n);else i(e,t,n)}function i(e,t,n){e.isStatic=!0,e.key=t,e.isOnce=n}t.prototype.$nextTick=function(e){return Yn(e,this)},t.prototype._render=function(){var t=this,n=t.$options,r=n.render,o=n.staticRenderFns,i=n._parentVnode;if(t._isMounted)for(var a in t.$slots)t.$slots[a]=Y(t.$slots[a]);i&&i.data.scopedSlots&&(t.$scopedSlots=i.data.scopedSlots),o&&!t._staticTrees&&(t._staticTrees=[]),t.$vnode=i;var s;try{s=r.call(t._renderProxy,t.$createElement)}catch(n){if(!In.errorHandler)throw"production"!==e.env.NODE_ENV&&Zn("Error when rendering "+Kn(t)+":"),n;In.errorHandler.call(null,n,t),s=t._vnode}return s instanceof _r||("production"!==e.env.NODE_ENV&&Array.isArray(s)&&Zn("Multiple root nodes returned from render function. Render function should return a single root node.",t),s=wr()),s.parent=i,s},t.prototype._s=r,t.prototype._v=W,t.prototype._n=o,t.prototype._e=wr,t.prototype._q=_,t.prototype._i=g,t.prototype._m=function(e,t){var r=this._staticTrees[e];return r&&!t?Array.isArray(r)?Y(r):J(r):(r=this._staticTrees[e]=this.$options.staticRenderFns[e].call(this._renderProxy),n(r,"__static__"+e,!1),r)},t.prototype._o=function(e,t,r){return n(e,"__once__"+t+(r?"_"+r:""),!0),e},t.prototype._f=function(e){return U(this.$options,"filters",e,!0)||Vn},t.prototype._l=function(e,t){var n,r,o,i,a;if(Array.isArray(e)||"string"==typeof e)for(n=new Array(e.length),r=0,o=e.length;r<o;r++)n[r]=t(e[r],r);else if("number"==typeof e)for(n=new Array(e),r=0;r<e;r++)n[r]=t(r+1,r);else if(p(e))for(i=Object.keys(e),n=new Array(i.length),r=0,o=i.length;r<o;r++)a=i[r],n[r]=t(e[a],a,r);return n},t.prototype._t=function(t,n,r,o){var i=this.$scopedSlots[t];if(i)return r=r||{},o&&f(r,o),i(r)||n;var a=this.$slots[t];return a&&"production"!==e.env.NODE_ENV&&(a._rendered&&Zn('Duplicate presence of slot "'+t+'" found in the same render tree - this will likely cause render errors.',this),a._rendered=!0),a||n},t.prototype._b=function(t,n,r,o){if(r)if(p(r)){Array.isArray(r)&&(r=h(r));for(var i in r)if("class"===i||"style"===i)t[i]=r[i];else{var a=t.attrs&&t.attrs.type,s=o||In.mustUseProp(n,a,i)?t.domProps||(t.domProps={}):t.attrs||(t.attrs={});s[i]=r[i]}}else"production"!==e.env.NODE_ENV&&Zn("v-bind without argument expects an Object or Array value",this);return t},t.prototype._k=function(e,t,n){var r=In.keyCodes[t]||n;return Array.isArray(r)?r.indexOf(e)===-1:r!==e}}function be(e,t){var n={};if(!e)return n;for(var r,o,i=[],a=0,s=e.length;a<s;a++)if(o=e[a],(o.context===t||o.functionalContext===t)&&o.data&&(r=o.data.slot)){var c=n[r]||(n[r]=[]);"template"===o.tag?c.push.apply(c,o.children):c.push(o)}else i.push(o);return i.length&&(1!==i.length||" "!==i[0].text&&!i[0].isComment)&&(n.default=i),n}function we(e){e._events=Object.create(null),e._hasHookEvent=!1;var t=e.$options._parentListeners;t&&Ee(e,t)}function xe(e,t,n){n?br.$once(e,t):br.$on(e,t)}function Ce(e,t){br.$off(e,t)}function Ee(e,t,n){br=e,le(t,n||{},xe,Ce,e)}function Oe(e){var t=/^hook:/;e.prototype.$on=function(e,n){var r=this;return(r._events[e]||(r._events[e]=[])).push(n),t.test(e)&&(r._hasHookEvent=!0),r},e.prototype.$once=function(e,t){function n(){r.$off(e,n),t.apply(r,arguments)}var r=this;return n.fn=t,r.$on(e,n),r},e.prototype.$off=function(e,t){var n=this;if(!arguments.length)return n._events=Object.create(null),n;var r=n._events[e];if(!r)return n;if(1===arguments.length)return n._events[e]=null,n;for(var o,i=r.length;i--;)if(o=r[i],o===t||o.fn===t){r.splice(i,1);break}return n},e.prototype.$emit=function(e){var t=this,n=t._events[e];if(n){n=n.length>1?d(n):n;for(var r=d(arguments,1),o=0,i=n.length;o<i;o++)n[o].apply(t,r)}return t}}function $e(e){var t=e.$options,n=t.parent;if(n&&!t.abstract){for(;n.$options.abstract&&n.$parent;)n=n.$parent;n.$children.push(e)}e.$parent=n,e.$root=n?n.$root:e,e.$children=[],e.$refs={},e._watcher=null,e._inactive=!1,e._isMounted=!1,e._isDestroyed=!1,e._isBeingDestroyed=!1}function Ae(t){t.prototype._mount=function(t,n){var r=this;return r.$el=t,r.$options.render||(r.$options.render=wr,"production"!==e.env.NODE_ENV&&(r.$options.template&&"#"!==r.$options.template.charAt(0)?Zn("You are using the runtime-only build of Vue where the template option is not available. Either pre-compile the templates into render functions, or use the compiler-included build.",r):Zn("Failed to mount component: template or render function not defined.",r))),ke(r,"beforeMount"),r._watcher=new Pr(r,function(){r._update(r._render(),n)},m),n=!1,null==r.$vnode&&(r._isMounted=!0,ke(r,"mounted")),r},t.prototype._update=function(e,t){var n=this;n._isMounted&&ke(n,"beforeUpdate");var r=n.$el,o=n._vnode,i=Ar;Ar=n,n._vnode=e,o?n.$el=n.__patch__(o,e):n.$el=n.__patch__(n.$el,e,t,!1,n.$options._parentElm,n.$options._refElm),Ar=i,r&&(r.__vue__=null),n.$el&&(n.$el.__vue__=n),n.$vnode&&n.$parent&&n.$vnode===n.$parent._vnode&&(n.$parent.$el=n.$el)},t.prototype._updateFromParent=function(t,n,r,o){var i=this,a=!(!i.$options._renderChildren&&!o);if(i.$options._parentVnode=r,i.$vnode=r,i._vnode&&(i._vnode.parent=r),i.$options._renderChildren=o,t&&i.$options.props){ar.shouldConvert=!1,"production"!==e.env.NODE_ENV&&(ar.isSettingProps=!0);for(var s=i.$options._propKeys||[],c=0;c<s.length;c++){var u=s[c];i[u]=F(u,i.$options.props,t,i)}ar.shouldConvert=!0,"production"!==e.env.NODE_ENV&&(ar.isSettingProps=!1),i.$options.propsData=t}if(n){var l=i.$options._parentListeners;i.$options._parentListeners=n,Ee(i,n,l)}a&&(i.$slots=be(o,r.context),i.$forceUpdate())},t.prototype.$forceUpdate=function(){var e=this;e._watcher&&e._watcher.update()},t.prototype.$destroy=function(){var e=this;if(!e._isBeingDestroyed){ke(e,"beforeDestroy"),e._isBeingDestroyed=!0;var t=e.$parent;!t||t._isBeingDestroyed||e.$options.abstract||a(t.$children,e),e._watcher&&e._watcher.teardown();for(var n=e._watchers.length;n--;)e._watchers[n].teardown();e._data.__ob__&&e._data.__ob__.vmCount--,e._isDestroyed=!0,ke(e,"destroyed"),e.$off(),e.$el&&(e.$el.__vue__=null),e.__patch__(e._vnode,null)}}}function ke(e,t){var n=e.$options[t];if(n)for(var r=0,o=n.length;r<o;r++)n[r].call(e);e._hasHookEvent&&e.$emit("hook:"+t)}function Ne(){kr.length=0,Nr={},"production"!==e.env.NODE_ENV&&(Sr={}),Dr=jr=!1}function Se(){jr=!0;var t,n,r;for(kr.sort(function(e,t){return e.id-t.id}),Tr=0;Tr<kr.length;Tr++)if(t=kr[Tr],n=t.id,Nr[n]=null,t.run(),"production"!==e.env.NODE_ENV&&null!=Nr[n]&&(Sr[n]=(Sr[n]||0)+1,Sr[n]>In._maxUpdateCount)){Zn("You may have an infinite update loop "+(t.user?'in watcher with expression "'+t.expression+'"':"in a component render function."),t.vm);break}for(Tr=kr.length;Tr--;)t=kr[Tr],r=t.vm,r._watcher===t&&r._isMounted&&ke(r,"updated");Jn&&In.devtools&&Jn.emit("flush"),Ne()}function De(e){var t=e.id;if(null==Nr[t]){if(Nr[t]=!0,jr){for(var n=kr.length-1;n>=0&&kr[n].id>e.id;)n--;kr.splice(Math.max(n,Tr)+1,0,e)}else kr.push(e);Dr||(Dr=!0,Yn(Se))}}function je(e){Vr.clear(),Te(e,Vr)}function Te(e,t){var n,r,o=Array.isArray(e);if((o||p(e))&&Object.isExtensible(e)){if(e.__ob__){var i=e.__ob__.dep.id;if(t.has(i))return;t.add(i)}if(o)for(n=e.length;n--;)Te(e[n],t);else for(r=Object.keys(e),n=r.length;n--;)Te(e[r[n]],t)}}function Me(e){e._watchers=[];var t=e.$options;t.props&&Pe(e,t.props),t.methods&&Re(e,t.methods),t.data?Ve(e):k(e._data={},!0),t.computed&&Ie(e,t.computed),t.watch&&Ue(e,t.watch)}function Pe(t,n){var r=t.$options.propsData||{},o=t.$options._propKeys=Object.keys(n),i=!t.$parent;ar.shouldConvert=i;for(var a=function(i){var a=o[i];"production"!==e.env.NODE_ENV?(Ir[a]&&Zn('"'+a+'" is a reserved attribute and cannot be used as component prop.',t),N(t,a,F(a,n,r,t),function(){t.$parent&&!ar.isSettingProps&&Zn("Avoid mutating a prop directly since the value will be overwritten whenever the parent component re-renders. Instead, use a data or computed property based on the prop's value. Prop being mutated: \""+a+'"',t)})):N(t,a,F(a,n,r,t))},s=0;s<o.length;s++)a(s);ar.shouldConvert=!0}function Ve(t){var n=t.$options.data;n=t._data="function"==typeof n?n.call(t):n||{},v(n)||(n={},"production"!==e.env.NODE_ENV&&Zn("data functions should return an object:\nhttps://vuejs.org/v2/guide/components.html#data-Must-Be-a-Function",t));for(var r=Object.keys(n),o=t.$options.props,i=r.length;i--;)o&&s(o,r[i])?"production"!==e.env.NODE_ENV&&Zn('The data property "'+r[i]+'" is already declared as a prop. Use prop default value instead.',t):qe(t,r[i]);k(n,!0)}function Ie(t,n){for(var r in n){"production"!==e.env.NODE_ENV&&r in t&&Zn('existing instance property "'+r+'" will be overwritten by a computed property with the same name.',t);var o=n[r];"function"==typeof o?(Lr.get=Le(o,t),Lr.set=m):(Lr.get=o.get?o.cache!==!1?Le(o.get,t):l(o.get,t):m,Lr.set=o.set?l(o.set,t):m),Object.defineProperty(t,r,Lr)}}function Le(e,t){var n=new Pr(t,e,m,{lazy:!0});return function(){return n.dirty&&n.evaluate(),tr.target&&n.depend(),n.value}}function Re(t,n){for(var r in n)t[r]=null==n[r]?m:l(n[r],t),"production"!==e.env.NODE_ENV&&null==n[r]&&Zn('method "'+r+'" has an undefined value in the component definition. Did you reference the function correctly?',t)}function Ue(e,t){for(var n in t){var r=t[n];if(Array.isArray(r))for(var o=0;o<r.length;o++)Fe(e,n,r[o]);else Fe(e,n,r)}}function Fe(e,t,n){var r;v(n)&&(r=n,n=n.handler),"string"==typeof n&&(n=e[n]),e.$watch(t,n,r)}function He(t){var n={};n.get=function(){return this._data},"production"!==e.env.NODE_ENV&&(n.set=function(e){Zn("Avoid replacing instance root $data. Use nested data properties instead.",this)}),Object.defineProperty(t.prototype,"$data",n),t.prototype.$set=S,t.prototype.$delete=D,t.prototype.$watch=function(e,t,n){var r=this;n=n||{},n.user=!0;var o=new Pr(r,e,t,n);return n.immediate&&t.call(r,o.value),function(){o.teardown()}}}function qe(e,t){b(t)||Object.defineProperty(e,t,{configurable:!0,enumerable:!0,get:function(){return e._data[t]},set:function(n){e._data[t]=n}})}function Be(t){t.prototype._init=function(t){var n=this;n._uid=Rr++,n._isVue=!0,t&&t._isComponent?ze(n,t):n.$options=R(Ge(n.constructor),t||{},n),"production"!==e.env.NODE_ENV?ur(n):n._renderProxy=n,n._self=n,$e(n),we(n),_e(n),ke(n,"beforeCreate"),Me(n),ke(n,"created"),n.$options.el&&n.$mount(n.$options.el)}}function ze(e,t){var n=e.$options=Object.create(e.constructor.options);n.parent=t.parent,n.propsData=t.propsData,n._parentVnode=t._parentVnode,n._parentListeners=t._parentListeners,n._renderChildren=t._renderChildren,n._componentTag=t._componentTag,n._parentElm=t._parentElm,n._refElm=t._refElm,t.render&&(n.render=t.render,n.staticRenderFns=t.staticRenderFns)}function Ge(e){var t=e.options;if(e.super){var n=e.super.options,r=e.superOptions,o=e.extendOptions;n!==r&&(e.superOptions=n,o.render=t.render,o.staticRenderFns=t.staticRenderFns,o._scopeId=t._scopeId,t=e.options=R(n,o),t.name&&(t.components[t.name]=e))}return t}function We(t){"production"===e.env.NODE_ENV||this instanceof We||Zn("Vue is a constructor and should be called with the `new` keyword"),this._init(t)}function Je(e){e.use=function(e){if(!e.installed){var t=d(arguments,1);return t.unshift(this),"function"==typeof e.install?e.install.apply(e,t):e.apply(null,t),e.installed=!0,this}}}function Ye(e){e.mixin=function(e){this.options=R(this.options,e)}}function Ke(t){t.cid=0;var n=1;t.extend=function(t){t=t||{};var r=this,o=r.cid,i=t._Ctor||(t._Ctor={});if(i[o])return i[o];var a=t.name||r.options.name;"production"!==e.env.NODE_ENV&&(/^[a-zA-Z][\w-]*$/.test(a)||Zn('Invalid component name: "'+a+'". Component names can only contain alphanumeric characters and the hyphen, and must start with a letter.'));var s=function(e){this._init(e)};return s.prototype=Object.create(r.prototype),s.prototype.constructor=s,s.cid=n++,s.options=R(r.options,t),s.super=r,s.extend=r.extend,s.mixin=r.mixin,s.use=r.use,In._assetTypes.forEach(function(e){s[e]=r[e]}),a&&(s.options.components[a]=s),s.superOptions=r.options,s.extendOptions=t,i[o]=s,s}}function Ze(t){In._assetTypes.forEach(function(n){t[n]=function(t,r){return r?("production"!==e.env.NODE_ENV&&"component"===n&&In.isReservedTag(t)&&Zn("Do not use built-in or reserved HTML elements as component id: "+t),"component"===n&&v(r)&&(r.name=r.name||t,r=this.options._base.extend(r)),"directive"===n&&"function"==typeof r&&(r={bind:r,update:r}),this.options[n+"s"][t]=r,r):this.options[n+"s"][t]}})}function Qe(e){return e&&(e.Ctor.options.name||e.tag)}function Xe(e,t){return"string"==typeof e?e.split(",").indexOf(t)>-1:e.test(t)}function et(e,t){for(var n in e){var r=e[n];if(r){var o=Qe(r.componentOptions);o&&!t(o)&&(tt(r),e[n]=null)}}}function tt(e){e&&(e.componentInstance._inactive||ke(e.componentInstance,"deactivated"),e.componentInstance.$destroy())}function nt(t){var n={};n.get=function(){return In},"production"!==e.env.NODE_ENV&&(n.set=function(){Zn("Do not replace the Vue.config object, set individual fields instead.")}),Object.defineProperty(t,"config",n),t.util=dr,t.set=S,t.delete=D,t.nextTick=Yn,t.options=Object.create(null),In._assetTypes.forEach(function(e){t.options[e+"s"]=Object.create(null)}),t.options._base=t,f(t.options.components,Hr),Je(t),Ye(t),Ke(t),Ze(t)}function rt(e){for(var t=e.data,n=e,r=e;r.componentInstance;)r=r.componentInstance._vnode,r.data&&(t=ot(r.data,t));for(;n=n.parent;)n.data&&(t=ot(t,n.data));return it(t)}function ot(e,t){return{staticClass:at(e.staticClass,t.staticClass),class:e.class?[e.class,t.class]:t.class}}function it(e){var t=e.class,n=e.staticClass;return n||t?at(n,st(t)):""}function at(e,t){return e?t?e+" "+t:e:t||""}function st(e){var t="";if(!e)return t;if("string"==typeof e)return e;if(Array.isArray(e)){for(var n,r=0,o=e.length;r<o;r++)e[r]&&(n=st(e[r]))&&(t+=n+" ");return t.slice(0,-1)}if(p(e)){for(var i in e)e[i]&&(t+=i+" ");return t.slice(0,-1)}return t}function ct(e){return to(e)?"svg":"math"===e?"math":void 0}function ut(e){if(!Un)return!0;if(no(e))return!1;if(e=e.toLowerCase(),null!=ro[e])return ro[e];var t=document.createElement(e);return e.indexOf("-")>-1?ro[e]=t.constructor===window.HTMLUnknownElement||t.constructor===window.HTMLElement:ro[e]=/HTMLUnknownElement/.test(t.toString())}function lt(t){if("string"==typeof t){var n=t;if(t=document.querySelector(t),!t)return"production"!==e.env.NODE_ENV&&Zn("Cannot find element: "+n),document.createElement("div")}return t}function dt(e,t){var n=document.createElement(e);return"select"!==e?n:(t.data&&t.data.attrs&&"multiple"in t.data.attrs&&n.setAttribute("multiple","multiple"),
n)}function ft(e,t){return document.createElementNS(Xr[e],t)}function pt(e){return document.createTextNode(e)}function vt(e){return document.createComment(e)}function ht(e,t,n){e.insertBefore(t,n)}function mt(e,t){e.removeChild(t)}function yt(e,t){e.appendChild(t)}function _t(e){return e.parentNode}function gt(e){return e.nextSibling}function bt(e){return e.tagName}function wt(e,t){e.textContent=t}function xt(e,t,n){e.setAttribute(t,n)}function Ct(e,t){var n=e.data.ref;if(n){var r=e.context,o=e.componentInstance||e.elm,i=r.$refs;t?Array.isArray(i[n])?a(i[n],o):i[n]===o&&(i[n]=void 0):e.data.refInFor?Array.isArray(i[n])&&i[n].indexOf(o)<0?i[n].push(o):i[n]=[o]:i[n]=o}}function Et(e){return null==e}function Ot(e){return null!=e}function $t(e,t){return e.key===t.key&&e.tag===t.tag&&e.isComment===t.isComment&&!e.data==!t.data}function At(e,t,n){var r,o,i={};for(r=t;r<=n;++r)o=e[r].key,Ot(o)&&(i[o]=r);return i}function kt(t){function n(e){return new _r(N.tagName(e).toLowerCase(),{},[],void 0,e)}function r(e,t){function n(){0===--n.listeners&&o(e)}return n.listeners=t,n}function o(e){var t=N.parentNode(e);t&&N.removeChild(t,e)}function a(t,n,r,o,i){if(t.isRootInsert=!i,!s(t,n,r,o)){var a=t.data,c=t.children,u=t.tag;Ot(u)?("production"!==e.env.NODE_ENV&&(a&&a.pre&&S++,S||t.ns||In.ignoredElements.length&&In.ignoredElements.indexOf(u)>-1||!In.isUnknownElement(u)||Zn("Unknown custom element: <"+u+'> - did you register the component correctly? For recursive components, make sure to provide the "name" option.',t.context)),t.elm=t.ns?N.createElementNS(t.ns,u):N.createElement(u,t),h(t),f(t,c,n),Ot(a)&&v(t,n),d(r,t.elm,o),"production"!==e.env.NODE_ENV&&a&&a.pre&&S--):t.isComment?(t.elm=N.createComment(t.text),d(r,t.elm,o)):(t.elm=N.createTextNode(t.text),d(r,t.elm,o))}}function s(e,t,n,r){var o=e.data;if(Ot(o)){var i=Ot(e.componentInstance)&&o.keepAlive;if(Ot(o=o.hook)&&Ot(o=o.init)&&o(e,!1,n,r),Ot(e.componentInstance))return u(e,t),i&&l(e,t,n,r),!0}}function u(e,t){e.data.pendingInsert&&t.push.apply(t,e.data.pendingInsert),e.elm=e.componentInstance.$el,p(e)?(v(e,t),h(e)):(Ct(e),t.push(e))}function l(e,t,n,r){for(var o,i=e;i.componentInstance;)if(i=i.componentInstance._vnode,Ot(o=i.data)&&Ot(o=o.transition)){for(o=0;o<A.activate.length;++o)A.activate[o](ao,i);t.push(i);break}d(n,e.elm,r)}function d(e,t,n){e&&(n?N.insertBefore(e,t,n):N.appendChild(e,t))}function f(e,t,n){if(Array.isArray(t))for(var r=0;r<t.length;++r)a(t[r],n,e.elm,null,!0);else c(e.text)&&N.appendChild(e.elm,N.createTextNode(e.text))}function p(e){for(;e.componentInstance;)e=e.componentInstance._vnode;return Ot(e.tag)}function v(e,t){for(var n=0;n<A.create.length;++n)A.create[n](ao,e);O=e.data.hook,Ot(O)&&(O.create&&O.create(ao,e),O.insert&&t.push(e))}function h(e){var t;Ot(t=e.context)&&Ot(t=t.$options._scopeId)&&N.setAttribute(e.elm,t,""),Ot(t=Ar)&&t!==e.context&&Ot(t=t.$options._scopeId)&&N.setAttribute(e.elm,t,"")}function m(e,t,n,r,o,i){for(;r<=o;++r)a(n[r],i,e,t)}function y(e){var t,n,r=e.data;if(Ot(r))for(Ot(t=r.hook)&&Ot(t=t.destroy)&&t(e),t=0;t<A.destroy.length;++t)A.destroy[t](e);if(Ot(t=e.children))for(n=0;n<e.children.length;++n)y(e.children[n])}function _(e,t,n,r){for(;n<=r;++n){var i=t[n];Ot(i)&&(Ot(i.tag)?(g(i),y(i)):o(i.elm))}}function g(e,t){if(t||Ot(e.data)){var n=A.remove.length+1;for(t?t.listeners+=n:t=r(e.elm,n),Ot(O=e.componentInstance)&&Ot(O=O._vnode)&&Ot(O.data)&&g(O,t),O=0;O<A.remove.length;++O)A.remove[O](e,t);Ot(O=e.data.hook)&&Ot(O=O.remove)?O(e,t):t()}else o(e.elm)}function b(t,n,r,o,i){for(var s,c,u,l,d=0,f=0,p=n.length-1,v=n[0],h=n[p],y=r.length-1,g=r[0],b=r[y],x=!i;d<=p&&f<=y;)Et(v)?v=n[++d]:Et(h)?h=n[--p]:$t(v,g)?(w(v,g,o),v=n[++d],g=r[++f]):$t(h,b)?(w(h,b,o),h=n[--p],b=r[--y]):$t(v,b)?(w(v,b,o),x&&N.insertBefore(t,v.elm,N.nextSibling(h.elm)),v=n[++d],b=r[--y]):$t(h,g)?(w(h,g,o),x&&N.insertBefore(t,h.elm,v.elm),h=n[--p],g=r[++f]):(Et(s)&&(s=At(n,d,p)),c=Ot(g.key)?s[g.key]:null,Et(c)?(a(g,o,t,v.elm),g=r[++f]):(u=n[c],"production"===e.env.NODE_ENV||u||Zn("It seems there are duplicate keys that is causing an update error. Make sure each v-for item has a unique key."),$t(u,g)?(w(u,g,o),n[c]=void 0,x&&N.insertBefore(t,g.elm,v.elm),g=r[++f]):(a(g,o,t,v.elm),g=r[++f])));d>p?(l=Et(r[y+1])?null:r[y+1].elm,m(t,l,r,f,y,o)):f>y&&_(t,n,d,p)}function w(e,t,n,r){if(e!==t){if(t.isStatic&&e.isStatic&&t.key===e.key&&(t.isCloned||t.isOnce))return t.elm=e.elm,void(t.componentInstance=e.componentInstance);var o,i=t.data,a=Ot(i);a&&Ot(o=i.hook)&&Ot(o=o.prepatch)&&o(e,t);var s=t.elm=e.elm,c=e.children,u=t.children;if(a&&p(t)){for(o=0;o<A.update.length;++o)A.update[o](e,t);Ot(o=i.hook)&&Ot(o=o.update)&&o(e,t)}Et(t.text)?Ot(c)&&Ot(u)?c!==u&&b(s,c,u,n,r):Ot(u)?(Ot(e.text)&&N.setTextContent(s,""),m(s,null,u,0,u.length-1,n)):Ot(c)?_(s,c,0,c.length-1):Ot(e.text)&&N.setTextContent(s,""):e.text!==t.text&&N.setTextContent(s,t.text),a&&Ot(o=i.hook)&&Ot(o=o.postpatch)&&o(e,t)}}function x(e,t,n){if(n&&e.parent)e.parent.data.pendingInsert=t;else for(var r=0;r<t.length;++r)t[r].data.hook.insert(t[r])}function C(t,n,r){if("production"!==e.env.NODE_ENV&&!E(t,n))return!1;n.elm=t;var o=n.tag,i=n.data,a=n.children;if(Ot(i)&&(Ot(O=i.hook)&&Ot(O=O.init)&&O(n,!0),Ot(O=n.componentInstance)))return u(n,r),!0;if(Ot(o)){if(Ot(a))if(t.hasChildNodes()){for(var s=!0,c=t.firstChild,l=0;l<a.length;l++){if(!c||!C(c,a[l],r)){s=!1;break}c=c.nextSibling}if(!s||c)return"production"===e.env.NODE_ENV||"undefined"==typeof console||D||(D=!0,console.warn("Parent: ",t),console.warn("Mismatching childNodes vs. VNodes: ",t.childNodes,a)),!1}else f(n,a,r);if(Ot(i))for(var d in i)if(!j(d)){v(n,r);break}}else t.data!==n.text&&(t.data=n.text);return!0}function E(e,t){return t.tag?0===t.tag.indexOf("vue-component")||t.tag.toLowerCase()===(e.tagName&&e.tagName.toLowerCase()):e.nodeType===(t.isComment?8:3)}var O,$,A={},k=t.modules,N=t.nodeOps;for(O=0;O<so.length;++O)for(A[so[O]]=[],$=0;$<k.length;++$)void 0!==k[$][so[O]]&&A[so[O]].push(k[$][so[O]]);var S=0,D=!1,j=i("attrs,style,class,staticClass,staticStyle,key");return function(t,r,o,i,s,c){if(!r)return void(t&&y(t));var u=!1,l=[];if(t){var d=Ot(t.nodeType);if(!d&&$t(t,r))w(t,r,l,i);else{if(d){if(1===t.nodeType&&t.hasAttribute("server-rendered")&&(t.removeAttribute("server-rendered"),o=!0),o){if(C(t,r,l))return x(r,l,!0),t;"production"!==e.env.NODE_ENV&&Zn("The client-side rendered virtual DOM tree is not matching server-rendered content. This is likely caused by incorrect HTML markup, for example nesting block-level elements inside <p>, or missing <tbody>. Bailing hydration and performing full client-side render.")}t=n(t)}var f=t.elm,v=N.parentNode(f);if(a(r,l,f._leaveCb?null:v,N.nextSibling(f)),r.parent){for(var h=r.parent;h;)h.elm=r.elm,h=h.parent;if(p(r))for(var m=0;m<A.create.length;++m)A.create[m](ao,r.parent)}null!==v?_(v,[t],0,0):Ot(t.tag)&&y(t)}}else u=!0,a(r,l,s,c);return x(r,l,u),r.elm}}function Nt(e,t){(e.data.directives||t.data.directives)&&St(e,t)}function St(e,t){var n,r,o,i=e===ao,a=t===ao,s=Dt(e.data.directives,e.context),c=Dt(t.data.directives,t.context),u=[],l=[];for(n in c)r=s[n],o=c[n],r?(o.oldValue=r.value,Tt(o,"update",t,e),o.def&&o.def.componentUpdated&&l.push(o)):(Tt(o,"bind",t,e),o.def&&o.def.inserted&&u.push(o));if(u.length){var d=function(){for(var n=0;n<u.length;n++)Tt(u[n],"inserted",t,e)};i?ce(t.data.hook||(t.data.hook={}),"insert",d,"dir-insert"):d()}if(l.length&&ce(t.data.hook||(t.data.hook={}),"postpatch",function(){for(var n=0;n<l.length;n++)Tt(l[n],"componentUpdated",t,e)},"dir-postpatch"),!i)for(n in s)c[n]||Tt(s[n],"unbind",e,e,a)}function Dt(e,t){var n=Object.create(null);if(!e)return n;var r,o;for(r=0;r<e.length;r++)o=e[r],o.modifiers||(o.modifiers=uo),n[jt(o)]=o,o.def=U(t.$options,"directives",o.name,!0);return n}function jt(e){return e.rawName||e.name+"."+Object.keys(e.modifiers||{}).join(".")}function Tt(e,t,n,r,o){var i=e.def&&e.def[t];i&&i(n.elm,e,n,r,o)}function Mt(e,t){if(e.data.attrs||t.data.attrs){var n,r,o,i=t.elm,a=e.data.attrs||{},s=t.data.attrs||{};s.__ob__&&(s=t.data.attrs=f({},s));for(n in s)r=s[n],o=a[n],o!==r&&Pt(i,n,r);qn&&s.value!==a.value&&Pt(i,"value",s.value);for(n in a)null==s[n]&&(Kr(n)?i.removeAttributeNS(Yr,Zr(n)):Wr(n)||i.removeAttribute(n))}}function Pt(e,t,n){Jr(t)?Qr(n)?e.removeAttribute(t):e.setAttribute(t,t):Wr(t)?e.setAttribute(t,Qr(n)||"false"===n?"false":"true"):Kr(t)?Qr(n)?e.removeAttributeNS(Yr,Zr(t)):e.setAttributeNS(Yr,t,n):Qr(n)?e.removeAttribute(t):e.setAttribute(t,n)}function Vt(e,t){var n=t.elm,r=t.data,o=e.data;if(r.staticClass||r.class||o&&(o.staticClass||o.class)){var i=rt(t),a=n._transitionClasses;a&&(i=at(i,st(a))),i!==n._prevClass&&(n.setAttribute("class",i),n._prevClass=i)}}function It(e,t,n,r){if(n){var o=t,i=qr;t=function(n){Lt(e,t,r,i),1===arguments.length?o(n):o.apply(null,arguments)}}qr.addEventListener(e,t,r)}function Lt(e,t,n,r){(r||qr).removeEventListener(e,t,n)}function Rt(e,t){if(e.data.on||t.data.on){var n=t.data.on||{},r=e.data.on||{};qr=t.elm,le(n,r,It,Lt,t.context)}}function Ut(e,t){if(e.data.domProps||t.data.domProps){var n,r,o=t.elm,i=e.data.domProps||{},a=t.data.domProps||{};a.__ob__&&(a=t.data.domProps=f({},a));for(n in i)null==a[n]&&(o[n]="");for(n in a)if(r=a[n],"textContent"!==n&&"innerHTML"!==n||(t.children&&(t.children.length=0),r!==i[n]))if("value"===n){o._value=r;var s=null==r?"":String(r);Ft(o,t,s)&&(o.value=s)}else o[n]=r}}function Ft(e,t,n){return!e.composing&&("option"===t.tag||Ht(e,n)||qt(t,n))}function Ht(e,t){return document.activeElement!==e&&e.value!==t}function qt(e,t){var n=e.elm.value,r=e.elm._vModifiers;return r&&r.number||"number"===e.elm.type?o(n)!==o(t):r&&r.trim?n.trim()!==t.trim():n!==t}function Bt(e){var t=zt(e.style);return e.staticStyle?f(e.staticStyle,t):t}function zt(e){return Array.isArray(e)?h(e):"string"==typeof e?mo(e):e}function Gt(e,t){var n,r={};if(t)for(var o=e;o.componentInstance;)o=o.componentInstance._vnode,o.data&&(n=Bt(o.data))&&f(r,n);(n=Bt(e.data))&&f(r,n);for(var i=e;i=i.parent;)i.data&&(n=Bt(i.data))&&f(r,n);return r}function Wt(e,t){var n=t.data,r=e.data;if(n.staticStyle||n.style||r.staticStyle||r.style){var o,i,a=t.elm,s=e.data.staticStyle,c=e.data.style||{},u=s||c,l=zt(t.data.style)||{};t.data.style=l.__ob__?f({},l):l;var d=Gt(t,!0);for(i in u)null==d[i]&&go(a,i,"");for(i in d)o=d[i],o!==u[i]&&go(a,i,null==o?"":o)}}function Jt(e,t){if(t&&t.trim())if(e.classList)t.indexOf(" ")>-1?t.split(/\s+/).forEach(function(t){return e.classList.add(t)}):e.classList.add(t);else{var n=" "+e.getAttribute("class")+" ";n.indexOf(" "+t+" ")<0&&e.setAttribute("class",(n+t).trim())}}function Yt(e,t){if(t&&t.trim())if(e.classList)t.indexOf(" ")>-1?t.split(/\s+/).forEach(function(t){return e.classList.remove(t)}):e.classList.remove(t);else{for(var n=" "+e.getAttribute("class")+" ",r=" "+t+" ";n.indexOf(r)>=0;)n=n.replace(r," ");e.setAttribute("class",n.trim())}}function Kt(e){So(function(){So(e)})}function Zt(e,t){(e._transitionClasses||(e._transitionClasses=[])).push(t),Jt(e,t)}function Qt(e,t){e._transitionClasses&&a(e._transitionClasses,t),Yt(e,t)}function Xt(e,t,n){var r=en(e,t),o=r.type,i=r.timeout,a=r.propCount;if(!o)return n();var s=o===Eo?Ao:No,c=0,u=function(){e.removeEventListener(s,l),n()},l=function(t){t.target===e&&++c>=a&&u()};setTimeout(function(){c<a&&u()},i+1),e.addEventListener(s,l)}function en(e,t){var n,r=window.getComputedStyle(e),o=r[$o+"Delay"].split(", "),i=r[$o+"Duration"].split(", "),a=tn(o,i),s=r[ko+"Delay"].split(", "),c=r[ko+"Duration"].split(", "),u=tn(s,c),l=0,d=0;t===Eo?a>0&&(n=Eo,l=a,d=i.length):t===Oo?u>0&&(n=Oo,l=u,d=c.length):(l=Math.max(a,u),n=l>0?a>u?Eo:Oo:null,d=n?n===Eo?i.length:c.length:0);var f=n===Eo&&Do.test(r[$o+"Property"]);return{type:n,timeout:l,propCount:d,hasTransform:f}}function tn(e,t){for(;e.length<t.length;)e=e.concat(e);return Math.max.apply(null,t.map(function(t,n){return nn(t)+nn(e[n])}))}function nn(e){return 1e3*Number(e.slice(0,-1))}function rn(e,t){var n=e.elm;n._leaveCb&&(n._leaveCb.cancelled=!0,n._leaveCb());var r=an(e.data.transition);if(r&&!n._enterCb&&1===n.nodeType){for(var o=r.css,i=r.type,a=r.enterClass,s=r.enterToClass,c=r.enterActiveClass,u=r.appearClass,l=r.appearToClass,d=r.appearActiveClass,f=r.beforeEnter,p=r.enter,v=r.afterEnter,h=r.enterCancelled,m=r.beforeAppear,y=r.appear,_=r.afterAppear,g=r.appearCancelled,b=Ar,w=Ar.$vnode;w&&w.parent;)w=w.parent,b=w.context;var x=!b._isMounted||!e.isRootInsert;if(!x||y||""===y){var C=x?u:a,E=x?d:c,O=x?l:s,$=x?m||f:f,A=x&&"function"==typeof y?y:p,k=x?_||v:v,N=x?g||h:h,S=o!==!1&&!qn,D=A&&(A._length||A.length)>1,j=n._enterCb=sn(function(){S&&(Qt(n,O),Qt(n,E)),j.cancelled?(S&&Qt(n,C),N&&N(n)):k&&k(n),n._enterCb=null});e.data.show||ce(e.data.hook||(e.data.hook={}),"insert",function(){var t=n.parentNode,r=t&&t._pending&&t._pending[e.key];r&&r.tag===e.tag&&r.elm._leaveCb&&r.elm._leaveCb(),A&&A(n,j)},"transition-insert"),$&&$(n),S&&(Zt(n,C),Zt(n,E),Kt(function(){Zt(n,O),Qt(n,C),j.cancelled||D||Xt(n,i,j)})),e.data.show&&(t&&t(),A&&A(n,j)),S||D||j()}}}function on(e,t){function n(){y.cancelled||(e.data.show||((r.parentNode._pending||(r.parentNode._pending={}))[e.key]=e),l&&l(r),h&&(Zt(r,s),Zt(r,u),Kt(function(){Zt(r,c),Qt(r,s),y.cancelled||m||Xt(r,a,y)})),d&&d(r,y),h||m||y())}var r=e.elm;r._enterCb&&(r._enterCb.cancelled=!0,r._enterCb());var o=an(e.data.transition);if(!o)return t();if(!r._leaveCb&&1===r.nodeType){var i=o.css,a=o.type,s=o.leaveClass,c=o.leaveToClass,u=o.leaveActiveClass,l=o.beforeLeave,d=o.leave,f=o.afterLeave,p=o.leaveCancelled,v=o.delayLeave,h=i!==!1&&!qn,m=d&&(d._length||d.length)>1,y=r._leaveCb=sn(function(){r.parentNode&&r.parentNode._pending&&(r.parentNode._pending[e.key]=null),h&&(Qt(r,c),Qt(r,u)),y.cancelled?(h&&Qt(r,s),p&&p(r)):(t(),f&&f(r)),r._leaveCb=null});v?v(n):n()}}function an(e){if(e){if("object"==typeof e){var t={};return e.css!==!1&&f(t,jo(e.name||"v")),f(t,e),t}return"string"==typeof e?jo(e):void 0}}function sn(e){var t=!1;return function(){t||(t=!0,e())}}function cn(e,t){t.data.show||rn(t)}function un(t,n,r){var o=n.value,i=t.multiple;if(i&&!Array.isArray(o))return void("production"!==e.env.NODE_ENV&&Zn('<select multiple v-model="'+n.expression+'"> expects an Array value for its binding, but got '+Object.prototype.toString.call(o).slice(8,-1),r));for(var a,s,c=0,u=t.options.length;c<u;c++)if(s=t.options[c],i)a=g(o,dn(s))>-1,s.selected!==a&&(s.selected=a);else if(_(dn(s),o))return void(t.selectedIndex!==c&&(t.selectedIndex=c));i||(t.selectedIndex=-1)}function ln(e,t){for(var n=0,r=t.length;n<r;n++)if(_(dn(t[n]),e))return!1;return!0}function dn(e){return"_value"in e?e._value:e.value}function fn(e){e.target.composing=!0}function pn(e){e.target.composing=!1,vn(e.target,"input")}function vn(e,t){var n=document.createEvent("HTMLEvents");n.initEvent(t,!0,!0),e.dispatchEvent(n)}function hn(e){return!e.componentInstance||e.data&&e.data.transition?e:hn(e.componentInstance._vnode)}function mn(e){var t=e&&e.componentOptions;return t&&t.Ctor.options.abstract?mn(ve(t.children)):e}function yn(e){var t={},n=e.$options;for(var r in n.propsData)t[r]=e[r];var o=n._parentListeners;for(var i in o)t[Nn(i)]=o[i].fn;return t}function _n(e,t){return/\d-keep-alive$/.test(t.tag)?e("keep-alive"):null}function gn(e){for(;e=e.parent;)if(e.data.transition)return!0}function bn(e,t){return t.key===e.key&&t.tag===e.tag}function wn(e){e.elm._moveCb&&e.elm._moveCb(),e.elm._enterCb&&e.elm._enterCb()}function xn(e){e.data.newPos=e.elm.getBoundingClientRect()}function Cn(e){var t=e.data.pos,n=e.data.newPos,r=t.left-n.left,o=t.top-n.top;if(r||o){e.data.moved=!0;var i=e.elm.style;i.transform=i.WebkitTransform="translate("+r+"px,"+o+"px)",i.transitionDuration="0s"}}var En,On,$n=i("slot,component",!0),An=Object.prototype.hasOwnProperty,kn=/-(\w)/g,Nn=u(function(e){return e.replace(kn,function(e,t){return t?t.toUpperCase():""})}),Sn=u(function(e){return e.charAt(0).toUpperCase()+e.slice(1)}),Dn=/([^-])([A-Z])/g,jn=u(function(e){return e.replace(Dn,"$1-$2").replace(Dn,"$1-$2").toLowerCase()}),Tn=Object.prototype.toString,Mn="[object Object]",Pn=function(){return!1},Vn=function(e){return e},In={optionMergeStrategies:Object.create(null),silent:!1,devtools:"production"!==e.env.NODE_ENV,errorHandler:null,ignoredElements:[],keyCodes:Object.create(null),isReservedTag:Pn,isUnknownElement:Pn,getTagNamespace:m,parsePlatformTagName:Vn,mustUseProp:Pn,_assetTypes:["component","directive","filter"],_lifecycleHooks:["beforeCreate","created","beforeMount","mounted","beforeUpdate","updated","beforeDestroy","destroyed","activated","deactivated"],_maxUpdateCount:100},Ln=/[^\w.$]/,Rn="__proto__"in{},Un="undefined"!=typeof window,Fn=Un&&window.navigator.userAgent.toLowerCase(),Hn=Fn&&/msie|trident/.test(Fn),qn=Fn&&Fn.indexOf("msie 9.0")>0,Bn=Fn&&Fn.indexOf("edge/")>0,zn=Fn&&Fn.indexOf("android")>0,Gn=Fn&&/iphone|ipad|ipod|ios/.test(Fn),Wn=function(){return void 0===En&&(En=!Un&&"undefined"!=typeof n&&"server"===n.process.env.VUE_ENV),En},Jn=Un&&window.__VUE_DEVTOOLS_GLOBAL_HOOK__,Yn=function(){function e(){r=!1;var e=n.slice(0);n.length=0;for(var t=0;t<e.length;t++)e[t]()}var t,n=[],r=!1;if("undefined"!=typeof Promise&&C(Promise)){var o=Promise.resolve(),i=function(e){console.error(e)};t=function(){o.then(e).catch(i),Gn&&setTimeout(m)}}else if("undefined"==typeof MutationObserver||!C(MutationObserver)&&"[object MutationObserverConstructor]"!==MutationObserver.toString())t=function(){setTimeout(e,0)};else{var a=1,s=new MutationObserver(e),c=document.createTextNode(String(a));s.observe(c,{characterData:!0}),t=function(){a=(a+1)%2,c.data=String(a)}}return function(e,o){var i;if(n.push(function(){e&&e.call(o),i&&i(o)}),r||(r=!0,t()),!e&&"undefined"!=typeof Promise)return new Promise(function(e){i=e})}}();On="undefined"!=typeof Set&&C(Set)?Set:function(){function e(){this.set=Object.create(null)}return e.prototype.has=function(e){return this.set[e]===!0},e.prototype.add=function(e){this.set[e]=!0},e.prototype.clear=function(){this.set=Object.create(null)},e}();var Kn,Zn=m;if("production"!==e.env.NODE_ENV){var Qn="undefined"!=typeof console;Zn=function(e,t){Qn&&!In.silent&&console.error("[Vue warn]: "+e+" "+(t?Xn(Kn(t)):""))},Kn=function(e){if(e.$root===e)return"root instance";var t=e._isVue?e.$options.name||e.$options._componentTag:e.name;return(t?"component <"+t+">":"anonymous component")+(e._isVue&&e.$options.__file?" at "+e.$options.__file:"")};var Xn=function(e){return"anonymous component"===e&&(e+=' - use the "name" option for better debugging messages.'),"\n(found in "+e+")"}}var er=0,tr=function(){this.id=er++,this.subs=[]};tr.prototype.addSub=function(e){this.subs.push(e)},tr.prototype.removeSub=function(e){a(this.subs,e)},tr.prototype.depend=function(){tr.target&&tr.target.addDep(this)},tr.prototype.notify=function(){for(var e=this.subs.slice(),t=0,n=e.length;t<n;t++)e[t].update()},tr.target=null;var nr=[],rr=Array.prototype,or=Object.create(rr);["push","pop","shift","unshift","splice","sort","reverse"].forEach(function(e){var t=rr[e];w(or,e,function(){for(var n=arguments,r=arguments.length,o=new Array(r);r--;)o[r]=n[r];var i,a=t.apply(this,o),s=this.__ob__;switch(e){case"push":i=o;break;case"unshift":i=o;break;case"splice":i=o.slice(2)}return i&&s.observeArray(i),s.dep.notify(),a})});var ir=Object.getOwnPropertyNames(or),ar={shouldConvert:!0,isSettingProps:!1},sr=function(e){if(this.value=e,this.dep=new tr,this.vmCount=0,w(e,"__ob__",this),Array.isArray(e)){var t=Rn?$:A;t(e,or,ir),this.observeArray(e)}else this.walk(e)};sr.prototype.walk=function(e){for(var t=Object.keys(e),n=0;n<t.length;n++)N(e,t[n],e[t[n]])},sr.prototype.observeArray=function(e){for(var t=0,n=e.length;t<n;t++)k(e[t])};var cr=In.optionMergeStrategies;"production"!==e.env.NODE_ENV&&(cr.el=cr.propsData=function(e,t,n,r){return n||Zn('option "'+r+'" can only be used during instance creation with the `new` keyword.'),lr(e,t)}),cr.data=function(t,n,r){return r?t||n?function(){var e="function"==typeof n?n.call(r):n,o="function"==typeof t?t.call(r):void 0;return e?T(e,o):o}:void 0:n?"function"!=typeof n?("production"!==e.env.NODE_ENV&&Zn('The "data" option should be a function that returns a per-instance value in component definitions.',r),t):t?function(){return T(n.call(this),t.call(this))}:n:t},In._lifecycleHooks.forEach(function(e){cr[e]=M}),In._assetTypes.forEach(function(e){cr[e+"s"]=P}),cr.watch=function(e,t){if(!t)return e;if(!e)return t;var n={};f(n,e);for(var r in t){var o=n[r],i=t[r];o&&!Array.isArray(o)&&(o=[o]),n[r]=o?o.concat(i):[i]}return n},cr.props=cr.methods=cr.computed=function(e,t){if(!t)return e;if(!e)return t;var n=Object.create(null);return f(n,e),f(n,t),n};var ur,lr=function(e,t){return void 0===t?e:t},dr=Object.freeze({defineReactive:N,_toString:r,toNumber:o,makeMap:i,isBuiltInTag:$n,remove:a,hasOwn:s,isPrimitive:c,cached:u,camelize:Nn,capitalize:Sn,hyphenate:jn,bind:l,toArray:d,extend:f,isObject:p,isPlainObject:v,toObject:h,noop:m,no:Pn,identity:Vn,genStaticKeys:y,looseEqual:_,looseIndexOf:g,isReserved:b,def:w,parsePath:x,hasProto:Rn,inBrowser:Un,UA:Fn,isIE:Hn,isIE9:qn,isEdge:Bn,isAndroid:zn,isIOS:Gn,isServerRendering:Wn,devtools:Jn,nextTick:Yn,get _Set(){return On},mergeOptions:R,resolveAsset:U,get warn(){return Zn},get formatComponentName(){return Kn},validateProp:F});if("production"!==e.env.NODE_ENV){var fr=i("Infinity,undefined,NaN,isFinite,isNaN,parseFloat,parseInt,decodeURI,decodeURIComponent,encodeURI,encodeURIComponent,Math,Number,Date,Array,Object,Boolean,String,RegExp,Map,Set,JSON,Intl,require"),pr=function(e,t){Zn('Property or method "'+t+'" is not defined on the instance but referenced during render. Make sure to declare reactive data properties in the data option.',e)},vr="undefined"!=typeof Proxy&&Proxy.toString().match(/native code/);if(vr){var hr=i("stop,prevent,self,ctrl,shift,alt,meta");In.keyCodes=new Proxy(In.keyCodes,{set:function(e,t,n){return hr(t)?(Zn("Avoid overwriting built-in modifier in config.keyCodes: ."+t),!1):(e[t]=n,!0)}})}var mr={has:function e(t,n){var e=n in t,r=fr(n)||"_"===n.charAt(0);return e||r||pr(t,n),e||!r}},yr={get:function(e,t){return"string"!=typeof t||t in e||pr(e,t),e[t]}};ur=function(e){if(vr){var t=e.$options,n=t.render&&t.render._withStripped?yr:mr;e._renderProxy=new Proxy(e,n)}else e._renderProxy=e}}var _r=function(e,t,n,r,o,i,a){this.tag=e,this.data=t,this.children=n,this.text=r,this.elm=o,this.ns=void 0,this.context=i,this.functionalContext=void 0,this.key=t&&t.key,this.componentOptions=a,this.componentInstance=void 0,this.parent=void 0,this.raw=!1,this.isStatic=!1,this.isRootInsert=!0,this.isComment=!1,this.isCloned=!1,this.isOnce=!1},gr={child:{}};gr.child.get=function(){return this.componentInstance},Object.defineProperties(_r.prototype,gr);var br,wr=function(){var e=new _r;return e.text="",e.isComment=!0,e},xr={init:X,prepatch:ee,insert:te,destroy:ne},Cr=Object.keys(xr),Er=u(function(e){var t="~"===e.charAt(0);e=t?e.slice(1):e;var n="!"===e.charAt(0);return e=n?e.slice(1):e,{name:e,once:t,capture:n}}),Or=1,$r=2,Ar=null,kr=[],Nr={},Sr={},Dr=!1,jr=!1,Tr=0,Mr=0,Pr=function(t,n,r,o){this.vm=t,t._watchers.push(this),o?(this.deep=!!o.deep,this.user=!!o.user,this.lazy=!!o.lazy,this.sync=!!o.sync):this.deep=this.user=this.lazy=this.sync=!1,this.cb=r,this.id=++Mr,this.active=!0,this.dirty=this.lazy,this.deps=[],this.newDeps=[],this.depIds=new On,this.newDepIds=new On,this.expression="production"!==e.env.NODE_ENV?n.toString():"","function"==typeof n?this.getter=n:(this.getter=x(n),this.getter||(this.getter=function(){},"production"!==e.env.NODE_ENV&&Zn('Failed watching path: "'+n+'" Watcher only accepts simple dot-delimited paths. For full control, use a function instead.',t))),this.value=this.lazy?void 0:this.get()};Pr.prototype.get=function(){E(this);var e=this.getter.call(this.vm,this.vm);return this.deep&&je(e),O(),this.cleanupDeps(),e},Pr.prototype.addDep=function(e){var t=e.id;this.newDepIds.has(t)||(this.newDepIds.add(t),this.newDeps.push(e),this.depIds.has(t)||e.addSub(this))},Pr.prototype.cleanupDeps=function(){for(var e=this,t=this.deps.length;t--;){var n=e.deps[t];e.newDepIds.has(n.id)||n.removeSub(e)}var r=this.depIds;this.depIds=this.newDepIds,this.newDepIds=r,this.newDepIds.clear(),r=this.deps,this.deps=this.newDeps,this.newDeps=r,this.newDeps.length=0},Pr.prototype.update=function(){this.lazy?this.dirty=!0:this.sync?this.run():De(this)},Pr.prototype.run=function(){if(this.active){var t=this.get();if(t!==this.value||p(t)||this.deep){var n=this.value;if(this.value=t,this.user)try{this.cb.call(this.vm,t,n)}catch(t){if(!In.errorHandler)throw"production"!==e.env.NODE_ENV&&Zn('Error in watcher "'+this.expression+'"',this.vm),t;In.errorHandler.call(null,t,this.vm)}else this.cb.call(this.vm,t,n)}}},Pr.prototype.evaluate=function(){this.value=this.get(),this.dirty=!1},Pr.prototype.depend=function(){for(var e=this,t=this.deps.length;t--;)e.deps[t].depend()},Pr.prototype.teardown=function(){var e=this;if(this.active){this.vm._isBeingDestroyed||a(this.vm._watchers,this);for(var t=this.deps.length;t--;)e.deps[t].removeSub(e);this.active=!1}};var Vr=new On,Ir={key:1,ref:1,slot:1},Lr={enumerable:!0,configurable:!0,get:m,set:m},Rr=0;Be(We),He(We),Oe(We),Ae(We),ge(We);var Ur=[String,RegExp],Fr={name:"keep-alive",abstract:!0,props:{include:Ur,exclude:Ur},created:function(){this.cache=Object.create(null)},destroyed:function(){var e=this;for(var t in this.cache)tt(e.cache[t])},watch:{include:function(e){et(this.cache,function(t){return Xe(e,t)})},exclude:function(e){et(this.cache,function(t){return!Xe(e,t)})}},render:function(){var e=ve(this.$slots.default),t=e&&e.componentOptions;if(t){var n=Qe(t);if(n&&(this.include&&!Xe(this.include,n)||this.exclude&&Xe(this.exclude,n)))return e;var r=null==e.key?t.Ctor.cid+(t.tag?"::"+t.tag:""):e.key;this.cache[r]?e.componentInstance=this.cache[r].componentInstance:this.cache[r]=e,e.data.keepAlive=!0}return e}},Hr={KeepAlive:Fr};nt(We),Object.defineProperty(We.prototype,"$isServer",{get:Wn}),We.version="2.1.10";var qr,Br,zr=i("input,textarea,option,select"),Gr=function(e,t,n){return"value"===n&&zr(e)&&"button"!==t||"selected"===n&&"option"===e||"checked"===n&&"input"===e||"muted"===n&&"video"===e},Wr=i("contenteditable,draggable,spellcheck"),Jr=i("allowfullscreen,async,autofocus,autoplay,checked,compact,controls,declare,default,defaultchecked,defaultmuted,defaultselected,defer,disabled,enabled,formnovalidate,hidden,indeterminate,inert,ismap,itemscope,loop,multiple,muted,nohref,noresize,noshade,novalidate,nowrap,open,pauseonexit,readonly,required,reversed,scoped,seamless,selected,sortable,translate,truespeed,typemustmatch,visible"),Yr="http://www.w3.org/1999/xlink",Kr=function(e){return":"===e.charAt(5)&&"xlink"===e.slice(0,5)},Zr=function(e){return Kr(e)?e.slice(6,e.length):""},Qr=function(e){return null==e||e===!1},Xr={svg:"http://www.w3.org/2000/svg",math:"http://www.w3.org/1998/Math/MathML"},eo=i("html,body,base,head,link,meta,style,title,address,article,aside,footer,header,h1,h2,h3,h4,h5,h6,hgroup,nav,section,div,dd,dl,dt,figcaption,figure,hr,img,li,main,ol,p,pre,ul,a,b,abbr,bdi,bdo,br,cite,code,data,dfn,em,i,kbd,mark,q,rp,rt,rtc,ruby,s,samp,small,span,strong,sub,sup,time,u,var,wbr,area,audio,map,track,video,embed,object,param,source,canvas,script,noscript,del,ins,caption,col,colgroup,table,thead,tbody,td,th,tr,button,datalist,fieldset,form,input,label,legend,meter,optgroup,option,output,progress,select,textarea,details,dialog,menu,menuitem,summary,content,element,shadow,template"),to=i("svg,animate,circle,clippath,cursor,defs,desc,ellipse,filter,font-face,g,glyph,image,line,marker,mask,missing-glyph,path,pattern,polygon,polyline,rect,switch,symbol,text,textpath,tspan,use,view",!0),no=function(e){return eo(e)||to(e)},ro=Object.create(null),oo=Object.freeze({createElement:dt,createElementNS:ft,createTextNode:pt,createComment:vt,insertBefore:ht,removeChild:mt,appendChild:yt,parentNode:_t,nextSibling:gt,tagName:bt,setTextContent:wt,setAttribute:xt}),io={create:function(e,t){Ct(t)},update:function(e,t){e.data.ref!==t.data.ref&&(Ct(e,!0),Ct(t))},destroy:function(e){Ct(e,!0)}},ao=new _r("",{},[]),so=["create","activate","update","remove","destroy"],co={create:Nt,update:Nt,destroy:function(e){Nt(e,ao)}},uo=Object.create(null),lo=[io,co],fo={create:Mt,update:Mt},po={create:Vt,update:Vt},vo={create:Rt,update:Rt},ho={create:Ut,update:Ut},mo=u(function(e){var t={},n=/;(?![^(]*\))/g,r=/:(.+)/;return e.split(n).forEach(function(e){if(e){var n=e.split(r);n.length>1&&(t[n[0].trim()]=n[1].trim())}}),t}),yo=/^--/,_o=/\s*!important$/,go=function(e,t,n){yo.test(t)?e.style.setProperty(t,n):_o.test(n)?e.style.setProperty(t,n.replace(_o,""),"important"):e.style[wo(t)]=n},bo=["Webkit","Moz","ms"],wo=u(function(e){if(Br=Br||document.createElement("div"),e=Nn(e),"filter"!==e&&e in Br.style)return e;for(var t=e.charAt(0).toUpperCase()+e.slice(1),n=0;n<bo.length;n++){var r=bo[n]+t;if(r in Br.style)return r}}),xo={create:Wt,update:Wt},Co=Un&&!qn,Eo="transition",Oo="animation",$o="transition",Ao="transitionend",ko="animation",No="animationend";Co&&(void 0===window.ontransitionend&&void 0!==window.onwebkittransitionend&&($o="WebkitTransition",Ao="webkitTransitionEnd"),void 0===window.onanimationend&&void 0!==window.onwebkitanimationend&&(ko="WebkitAnimation",No="webkitAnimationEnd"));var So=Un&&window.requestAnimationFrame?window.requestAnimationFrame.bind(window):setTimeout,Do=/\b(transform|all)(,|$)/,jo=u(function(e){return{enterClass:e+"-enter",leaveClass:e+"-leave",appearClass:e+"-enter",enterToClass:e+"-enter-to",leaveToClass:e+"-leave-to",appearToClass:e+"-enter-to",enterActiveClass:e+"-enter-active",leaveActiveClass:e+"-leave-active",appearActiveClass:e+"-enter-active"}}),To=Un?{create:cn,activate:cn,remove:function(e,t){e.data.show?t():on(e,t)}}:{},Mo=[fo,po,vo,ho,xo,To],Po=Mo.concat(lo),Vo=kt({nodeOps:oo,modules:Po}),Io=/^input|select|textarea|vue-component-[0-9]+(-[0-9a-zA-Z_-]*)?$/;qn&&document.addEventListener("selectionchange",function(){var e=document.activeElement;e&&e.vmodel&&vn(e,"input")});var Lo={inserted:function(t,n,r){if("production"!==e.env.NODE_ENV&&(Io.test(r.tag)||Zn("v-model is not supported on element type: <"+r.tag+">. If you are working with contenteditable, it's recommended to wrap a library dedicated for that purpose inside a custom component.",r.context)),"select"===r.tag){var o=function(){un(t,n,r.context)};o(),(Hn||Bn)&&setTimeout(o,0)}else"textarea"!==r.tag&&"text"!==t.type||(t._vModifiers=n.modifiers,n.modifiers.lazy||(zn||(t.addEventListener("compositionstart",fn),t.addEventListener("compositionend",pn)),qn&&(t.vmodel=!0)))},componentUpdated:function(e,t,n){if("select"===n.tag){un(e,t,n.context);var r=e.multiple?t.value.some(function(t){return ln(t,e.options)}):t.value!==t.oldValue&&ln(t.value,e.options);r&&vn(e,"change")}}},Ro={bind:function(e,t,n){var r=t.value;n=hn(n);var o=n.data&&n.data.transition,i=e.__vOriginalDisplay="none"===e.style.display?"":e.style.display;r&&o&&!qn?(n.data.show=!0,rn(n,function(){e.style.display=i})):e.style.display=r?i:"none"},update:function(e,t,n){var r=t.value,o=t.oldValue;if(r!==o){n=hn(n);var i=n.data&&n.data.transition;i&&!qn?(n.data.show=!0,r?rn(n,function(){e.style.display=e.__vOriginalDisplay}):on(n,function(){e.style.display="none"})):e.style.display=r?e.__vOriginalDisplay:"none"}},unbind:function(e,t,n,r,o){o||(e.style.display=e.__vOriginalDisplay)}},Uo={model:Lo,show:Ro},Fo={name:String,appear:Boolean,css:Boolean,mode:String,type:String,enterClass:String,leaveClass:String,enterToClass:String,leaveToClass:String,enterActiveClass:String,leaveActiveClass:String,appearClass:String,appearActiveClass:String,appearToClass:String},Ho={name:"transition",props:Fo,abstract:!0,render:function(t){var n=this,r=this.$slots.default;if(r&&(r=r.filter(function(e){return e.tag}),r.length)){"production"!==e.env.NODE_ENV&&r.length>1&&Zn("<transition> can only be used on a single element. Use <transition-group> for lists.",this.$parent);var o=this.mode;"production"!==e.env.NODE_ENV&&o&&"in-out"!==o&&"out-in"!==o&&Zn("invalid <transition> mode: "+o,this.$parent);
var i=r[0];if(gn(this.$vnode))return i;var a=mn(i);if(!a)return i;if(this._leaving)return _n(t,i);var s="__transition-"+this._uid+"-",u=a.key=null==a.key?s+a.tag:c(a.key)?0===String(a.key).indexOf(s)?a.key:s+a.key:a.key,l=(a.data||(a.data={})).transition=yn(this),d=this._vnode,p=mn(d);if(a.data.directives&&a.data.directives.some(function(e){return"show"===e.name})&&(a.data.show=!0),p&&p.data&&!bn(a,p)){var v=p&&(p.data.transition=f({},l));if("out-in"===o)return this._leaving=!0,ce(v,"afterLeave",function(){n._leaving=!1,n.$forceUpdate()},u),_n(t,i);if("in-out"===o){var h,m=function(){h()};ce(l,"afterEnter",m,u),ce(l,"enterCancelled",m,u),ce(v,"delayLeave",function(e){h=e},u)}}return i}}},qo=f({tag:String,moveClass:String},Fo);delete qo.mode;var Bo={props:qo,render:function(t){for(var n=this.tag||this.$vnode.data.tag||"span",r=Object.create(null),o=this.prevChildren=this.children,i=this.$slots.default||[],a=this.children=[],s=yn(this),c=0;c<i.length;c++){var u=i[c];if(u.tag)if(null!=u.key&&0!==String(u.key).indexOf("__vlist"))a.push(u),r[u.key]=u,(u.data||(u.data={})).transition=s;else if("production"!==e.env.NODE_ENV){var l=u.componentOptions,d=l?l.Ctor.options.name||l.tag:u.tag;Zn("<transition-group> children must be keyed: <"+d+">")}}if(o){for(var f=[],p=[],v=0;v<o.length;v++){var h=o[v];h.data.transition=s,h.data.pos=h.elm.getBoundingClientRect(),r[h.key]?f.push(h):p.push(h)}this.kept=t(n,null,f),this.removed=p}return t(n,null,a)},beforeUpdate:function(){this.__patch__(this._vnode,this.kept,!1,!0),this._vnode=this.kept},updated:function(){var e=this.prevChildren,t=this.moveClass||(this.name||"v")+"-move";if(e.length&&this.hasMove(e[0].elm,t)){e.forEach(wn),e.forEach(xn),e.forEach(Cn);document.body.offsetHeight;e.forEach(function(e){if(e.data.moved){var n=e.elm,r=n.style;Zt(n,t),r.transform=r.WebkitTransform=r.transitionDuration="",n.addEventListener(Ao,n._moveCb=function e(r){r&&!/transform$/.test(r.propertyName)||(n.removeEventListener(Ao,e),n._moveCb=null,Qt(n,t))})}})}},methods:{hasMove:function(e,t){if(!Co)return!1;if(null!=this._hasMove)return this._hasMove;Zt(e,t);var n=en(e);return Qt(e,t),this._hasMove=n.hasTransform}}},zo={Transition:Ho,TransitionGroup:Bo};We.config.isUnknownElement=ut,We.config.isReservedTag=no,We.config.getTagNamespace=ct,We.config.mustUseProp=Gr,f(We.options.directives,Uo),f(We.options.components,zo),We.prototype.__patch__=Un?Vo:m,We.prototype.$mount=function(e,t){return e=e&&Un?lt(e):void 0,this._mount(e,t)},"production"!==e.env.NODE_ENV&&Un&&"undefined"!=typeof console&&console[console.info?"info":"log"]("You are running Vue in development mode.\nMake sure to turn on production mode when deploying for production.\nSee more tips at https://vuejs.org/guide/deployment.html"),setTimeout(function(){In.devtools&&(Jn?Jn.emit("init",We):"production"!==e.env.NODE_ENV&&Un&&!Bn&&/Chrome\/\d+/.test(window.navigator.userAgent)&&console[console.info?"info":"log"]("Download the Vue Devtools extension for a better development experience:\nhttps://github.com/vuejs/vue-devtools"))},0),t.exports=We}).call(this,e("_process"),"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{_process:2}],5:[function(e,t,n){!function(e,r){"object"==typeof n&&"undefined"!=typeof t?t.exports=r():"function"==typeof define&&define.amd?define(r):e.Vuex=r()}(this,function(){"use strict";function e(e){w&&(e._devtoolHook=w,w.emit("vuex:init",e),w.on("vuex:travel-to-state",function(t){e.replaceState(t)}),e.subscribe(function(e,t){w.emit("vuex:mutation",e,t)}))}function t(e){return Array.isArray(e)?e.map(function(e){return{key:e,val:e}}):Object.keys(e).map(function(t){return{key:t,val:e[t]}})}function n(e){return function(t,n){return"string"!=typeof t?(n=t,t=""):"/"!==t.charAt(t.length-1)&&(t+="/"),e(t,n)}}function r(e,t,n){var r=e._modulesNamespaceMap[n];return r||console.error("[vuex] module namespace not found in "+t+"(): "+n),r}function o(e,t){Object.keys(e).forEach(function(n){return t(e[n],n)})}function i(e){return null!==e&&"object"==typeof e}function a(e){return e&&"function"==typeof e.then}function s(e,t){if(!e)throw new Error("[vuex] "+t)}function c(e,t){if(e.update(t),t.modules)for(var n in t.modules){if(!e.getChild(n))return void console.warn("[vuex] trying to add a new module '"+n+"' on hot reloading, manual reload is needed");c(e.getChild(n),t.modules[n])}}function u(e,t){e._actions=Object.create(null),e._mutations=Object.create(null),e._wrappedGetters=Object.create(null),e._modulesNamespaceMap=Object.create(null);var n=e.state;d(e,n,[],e._modules.root,!0),l(e,n,t)}function l(e,t,n){var r=e._vm;e.getters={};var i=e._wrappedGetters,a={};o(i,function(t,n){a[n]=function(){return t(e)},Object.defineProperty(e.getters,n,{get:function(){return e._vm[n]},enumerable:!0})});var s=S.config.silent;S.config.silent=!0,e._vm=new S({data:{state:t},computed:a}),S.config.silent=s,e.strict&&y(e),r&&(n&&e._withCommit(function(){r.state=null}),S.nextTick(function(){return r.$destroy()}))}function d(e,t,n,r,o){var i=!n.length,a=e._modules.getNamespace(n);if(a&&(e._modulesNamespaceMap[a]=r),!i&&!o){var s=_(t,n.slice(0,-1)),c=n[n.length-1];e._withCommit(function(){S.set(s,c,r.state)})}var u=r.context=f(e,a,n);r.forEachMutation(function(t,n){var r=a+n;v(e,r,t,u)}),r.forEachAction(function(t,n){var r=a+n;h(e,r,t,u)}),r.forEachGetter(function(t,n){var r=a+n;m(e,r,t,u)}),r.forEachChild(function(r,i){d(e,t,n.concat(i),r,o)})}function f(e,t,n){var r=""===t,o={dispatch:r?e.dispatch:function(n,r,o){var i=g(n,r,o),a=i.payload,s=i.options,c=i.type;return s&&s.root||(c=t+c,e._actions[c])?e.dispatch(c,a):void console.error("[vuex] unknown local action type: "+i.type+", global type: "+c)},commit:r?e.commit:function(n,r,o){var i=g(n,r,o),a=i.payload,s=i.options,c=i.type;return s&&s.root||(c=t+c,e._mutations[c])?void e.commit(c,a,s):void console.error("[vuex] unknown local mutation type: "+i.type+", global type: "+c)}};return Object.defineProperties(o,{getters:{get:r?function(){return e.getters}:function(){return p(e,t)}},state:{get:function(){return _(e.state,n)}}}),o}function p(e,t){var n={},r=t.length;return Object.keys(e.getters).forEach(function(o){if(o.slice(0,r)===t){var i=o.slice(r);Object.defineProperty(n,i,{get:function(){return e.getters[o]},enumerable:!0})}}),n}function v(e,t,n,r){var o=e._mutations[t]||(e._mutations[t]=[]);o.push(function(e){n(r.state,e)})}function h(e,t,n,r){var o=e._actions[t]||(e._actions[t]=[]);o.push(function(t,o){var i=n({dispatch:r.dispatch,commit:r.commit,getters:r.getters,state:r.state,rootGetters:e.getters,rootState:e.state},t,o);return a(i)||(i=Promise.resolve(i)),e._devtoolHook?i.catch(function(t){throw e._devtoolHook.emit("vuex:error",t),t}):i})}function m(e,t,n,r){return e._wrappedGetters[t]?void console.error("[vuex] duplicate getter key: "+t):void(e._wrappedGetters[t]=function(e){return n(r.state,r.getters,e.state,e.getters)})}function y(e){e._vm.$watch("state",function(){s(e._committing,"Do not mutate vuex store state outside mutation handlers.")},{deep:!0,sync:!0})}function _(e,t){return t.length?t.reduce(function(e,t){return e[t]},e):e}function g(e,t,n){return i(e)&&e.type&&(n=t,t=e,e=e.type),s("string"==typeof e,"Expects string as the type, but found "+typeof e+"."),{type:e,payload:t,options:n}}function b(e){return S?void console.error("[vuex] already installed. Vue.use(Vuex) should be called only once."):(S=e,void x(S))}var w="undefined"!=typeof window&&window.__VUE_DEVTOOLS_GLOBAL_HOOK__,x=function(e){function t(){var e=this.$options;e.store?this.$store=e.store:e.parent&&e.parent.$store&&(this.$store=e.parent.$store)}var n=Number(e.version.split(".")[0]);if(n>=2){var r=e.config._lifecycleHooks.indexOf("init")>-1;e.mixin(r?{init:t}:{beforeCreate:t})}else{var o=e.prototype._init;e.prototype._init=function(e){void 0===e&&(e={}),e.init=e.init?[t].concat(e.init):t,o.call(this,e)}}},C=n(function(e,n){var o={};return t(n).forEach(function(t){var n=t.key,i=t.val;o[n]=function(){var t=this.$store.state,n=this.$store.getters;if(e){var o=r(this.$store,"mapState",e);if(!o)return;t=o.context.state,n=o.context.getters}return"function"==typeof i?i.call(this,t,n):t[i]}}),o}),E=n(function(e,n){var o={};return t(n).forEach(function(t){var n=t.key,i=t.val;i=e+i,o[n]=function(){for(var t=[],n=arguments.length;n--;)t[n]=arguments[n];if(!e||r(this.$store,"mapMutations",e))return this.$store.commit.apply(this.$store,[i].concat(t))}}),o}),O=n(function(e,n){var o={};return t(n).forEach(function(t){var n=t.key,i=t.val;i=e+i,o[n]=function(){if(!e||r(this.$store,"mapGetters",e))return i in this.$store.getters?this.$store.getters[i]:void console.error("[vuex] unknown getter: "+i)}}),o}),$=n(function(e,n){var o={};return t(n).forEach(function(t){var n=t.key,i=t.val;i=e+i,o[n]=function(){for(var t=[],n=arguments.length;n--;)t[n]=arguments[n];if(!e||r(this.$store,"mapActions",e))return this.$store.dispatch.apply(this.$store,[i].concat(t))}}),o}),A=function(e,t){this.runtime=t,this._children=Object.create(null),this._rawModule=e},k={state:{},namespaced:{}};k.state.get=function(){return this._rawModule.state||{}},k.namespaced.get=function(){return!!this._rawModule.namespaced},A.prototype.addChild=function(e,t){this._children[e]=t},A.prototype.removeChild=function(e){delete this._children[e]},A.prototype.getChild=function(e){return this._children[e]},A.prototype.update=function(e){this._rawModule.namespaced=e.namespaced,e.actions&&(this._rawModule.actions=e.actions),e.mutations&&(this._rawModule.mutations=e.mutations),e.getters&&(this._rawModule.getters=e.getters)},A.prototype.forEachChild=function(e){o(this._children,e)},A.prototype.forEachGetter=function(e){this._rawModule.getters&&o(this._rawModule.getters,e)},A.prototype.forEachAction=function(e){this._rawModule.actions&&o(this._rawModule.actions,e)},A.prototype.forEachMutation=function(e){this._rawModule.mutations&&o(this._rawModule.mutations,e)},Object.defineProperties(A.prototype,k);var N=function(e){var t=this;this.root=new A(e,!1),e.modules&&o(e.modules,function(e,n){t.register([n],e,!1)})};N.prototype.get=function(e){return e.reduce(function(e,t){return e.getChild(t)},this.root)},N.prototype.getNamespace=function(e){var t=this.root;return e.reduce(function(e,n){return t=t.getChild(n),e+(t.namespaced?n+"/":"")},"")},N.prototype.update=function(e){c(this.root,e)},N.prototype.register=function(e,t,n){var r=this;void 0===n&&(n=!0);var i=this.get(e.slice(0,-1)),a=new A(t,n);i.addChild(e[e.length-1],a),t.modules&&o(t.modules,function(t,o){r.register(e.concat(o),t,n)})},N.prototype.unregister=function(e){var t=this.get(e.slice(0,-1)),n=e[e.length-1];t.getChild(n).runtime&&t.removeChild(n)};var S,D=function(t){var n=this;void 0===t&&(t={}),s(S,"must call Vue.use(Vuex) before creating a store instance."),s("undefined"!=typeof Promise,"vuex requires a Promise polyfill in this browser.");var r=t.state;void 0===r&&(r={});var o=t.plugins;void 0===o&&(o=[]);var i=t.strict;void 0===i&&(i=!1),this._committing=!1,this._actions=Object.create(null),this._mutations=Object.create(null),this._wrappedGetters=Object.create(null),this._modules=new N(t),this._modulesNamespaceMap=Object.create(null),this._subscribers=[],this._watcherVM=new S;var a=this,c=this,u=c.dispatch,f=c.commit;this.dispatch=function(e,t){return u.call(a,e,t)},this.commit=function(e,t,n){return f.call(a,e,t,n)},this.strict=i,d(this,r,[],this._modules.root),l(this,r),o.concat(e).forEach(function(e){return e(n)})},j={state:{}};j.state.get=function(){return this._vm.$data.state},j.state.set=function(e){s(!1,"Use store.replaceState() to explicit replace store state.")},D.prototype.commit=function(e,t,n){var r=this,o=g(e,t,n),i=o.type,a=o.payload,s=o.options,c={type:i,payload:a},u=this._mutations[i];return u?(this._withCommit(function(){u.forEach(function(e){e(a)})}),this._subscribers.forEach(function(e){return e(c,r.state)}),void(s&&s.silent&&console.warn("[vuex] mutation type: "+i+". Silent option has been removed. Use the filter functionality in the vue-devtools"))):void console.error("[vuex] unknown mutation type: "+i)},D.prototype.dispatch=function(e,t){var n=g(e,t),r=n.type,o=n.payload,i=this._actions[r];return i?i.length>1?Promise.all(i.map(function(e){return e(o)})):i[0](o):void console.error("[vuex] unknown action type: "+r)},D.prototype.subscribe=function(e){var t=this._subscribers;return t.indexOf(e)<0&&t.push(e),function(){var n=t.indexOf(e);n>-1&&t.splice(n,1)}},D.prototype.watch=function(e,t,n){var r=this;return s("function"==typeof e,"store.watch only accepts a function."),this._watcherVM.$watch(function(){return e(r.state,r.getters)},t,n)},D.prototype.replaceState=function(e){var t=this;this._withCommit(function(){t._vm.state=e})},D.prototype.registerModule=function(e,t){"string"==typeof e&&(e=[e]),s(Array.isArray(e),"module path must be a string or an Array."),this._modules.register(e,t),d(this,this.state,e,this._modules.get(e)),l(this,this.state)},D.prototype.unregisterModule=function(e){var t=this;"string"==typeof e&&(e=[e]),s(Array.isArray(e),"module path must be a string or an Array."),this._modules.unregister(e),this._withCommit(function(){var n=_(t.state,e.slice(0,-1));S.delete(n,e[e.length-1])}),u(this)},D.prototype.hotUpdate=function(e){this._modules.update(e),u(this,!0)},D.prototype._withCommit=function(e){var t=this._committing;this._committing=!0,e(),this._committing=t},Object.defineProperties(D.prototype,j),"undefined"!=typeof window&&window.Vue&&b(window.Vue);var T={Store:D,install:b,version:"2.1.2",mapState:C,mapMutations:E,mapGetters:O,mapActions:$};return T})},{}],6:[function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),i=e("path"),a=function(){function e(){r(this,e);var t=Joomla.getOptions("com_media",{});if(void 0===t.apiBaseUrl)throw new TypeError("Media api baseUrl is not defined");this._baseUrl=t.apiBaseUrl}return o(e,[{key:"getContents",value:function(e){var t=this;return new Promise(function(n,r){var o=t._baseUrl+"&task=api.files&path="+e;jQuery.getJSON(o).success(function(e){return n(t._normalizeArray(e.data))}).fail(function(e,t,n){r(e)})}).catch(this._handleError)}},{key:"_normalizeArray",value:function(e){var t=e.filter(function(e){return"dir"===e.type}).map(function(e){return e.directory=i.dirname(e.path),e.directories=[],e.files=[],e}),n=e.filter(function(e){return"file"===e.type}).map(function(e){return e.directory=i.dirname(e.path),e});return{directories:t,files:n}}},{key:"_handleError",value:function(e){switch(alert(e.status+" "+e.statusText),e.status){case 404:break;case 401:case 403:case 500:window.location.href="/administrator";default:window.location.href="/administrator"}throw e}}]),e}();n.api=new a},{path:1}],7:[function(e,t,n){!function(){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default={name:"media-app",methods:{setFullHeight:function(){this.fullHeight=window.innerHeight-this.$el.offsetTop+"px"}},data:function(){return{fullHeight:""}},mounted:function(){var e=this;this.$store.dispatch("getContents",this.$store.state.selectedDirectory),this.$nextTick(function(){e.setFullHeight(),window.addEventListener("resize",e.setFullHeight)})},beforeDestroy:function(){window.removeEventListener("resize",this.setFullHeight)}}}(),t.exports.__esModule&&(t.exports=t.exports.default);var r="function"==typeof t.exports?t.exports.options:t.exports;r.functional&&console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions."),r.render=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"media-container",style:{minHeight:e.fullHeight}},[n("media-toolbar"),e._v(" "),n("div",{staticClass:"media-main"},[n("div",{staticClass:"media-sidebar"},[n("media-tree",{attrs:{root:"/"}})],1),e._v(" "),n("media-browser")],1)],1)},r.staticRenderFns=[],t.hot&&!function(){var n=e("vue-hot-reload-api");n.install(e("vue"),!0),n.compatible&&(t.hot.accept(),t.hot.data?n.reload("data-v-f6047410",r):n.createRecord("data-v-f6047410",r))}()},{vue:4,"vue-hot-reload-api":3}],8:[function(e,t,n){!function(){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default={name:"media-breadcrumb",computed:{crumbs:function(){var e=this,t=[];return this.$store.state.selectedDirectory.split("/").filter(function(e){return 0!==e.length}).forEach(function(n){t.push({name:n,path:e.$store.state.selectedDirectory.split(n)[0]+n})}),t},isLast:function(e){return this.crumbs.indexOf(e)===this.crumbs.length-1}},methods:{goTo:function(e){this.$store.dispatch("getContents",e)}}}}(),t.exports.__esModule&&(t.exports=t.exports.default);var r="function"==typeof t.exports?t.exports.options:t.exports;r.functional&&console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions."),r.render=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("ul",{staticClass:"media-breadcrumb"},[n("li",[n("a",{on:{click:function(t){t.stopPropagation(),t.preventDefault(),e.goTo("/")}}},[e._v("Home")])]),e._v(" "),e._l(e.crumbs,function(t){return n("li",[n("span",{staticClass:"divider material-icons"},[e._v("keyboard_arrow_right")]),e._v(" "),n("a",{on:{click:function(n){n.stopPropagation(),n.preventDefault(),e.goTo(t.path)}}},[e._v(e._s(t.name))])])})],2)},r.staticRenderFns=[],t.hot&&!function(){var n=e("vue-hot-reload-api");n.install(e("vue"),!0),n.compatible&&(t.hot.accept(),t.hot.data?n.reload("data-v-190156b4",r):n.createRecord("data-v-190156b4",r))}()},{vue:4,"vue-hot-reload-api":3}],9:[function(e,t,n){!function(){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default={name:"media-browser",computed:{items:function(){return this.$store.getters.getSelectedDirectoryContents}}}}(),t.exports.__esModule&&(t.exports=t.exports.default);var r="function"==typeof t.exports?t.exports.options:t.exports;r.functional&&console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions."),r.render=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"media-browser"},[n("div",{staticClass:"media-browser-items"},e._l(e.items,function(e){return n("media-browser-item",{attrs:{item:e}})}))])},r.staticRenderFns=[],t.hot&&!function(){var n=e("vue-hot-reload-api");n.install(e("vue"),!0),n.compatible&&(t.hot.accept(),t.hot.data?n.reload("data-v-59bb0098",r):n.createRecord("data-v-59bb0098",r))}()},{vue:4,"vue-hot-reload-api":3}],10:[function(e,t,n){!function(){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default={name:"media-browser-item-directory",props:["item"],methods:{goTo:function(e){this.$store.dispatch("getContents",e)},select:function(e){}}}}(),t.exports.__esModule&&(t.exports=t.exports.default);var r="function"==typeof t.exports?t.exports.options:t.exports;r.functional&&console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions."),r.render=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"media-browser-item-directory",on:{click:function(t){e.select(e.item)},dblclick:function(t){e.goTo(e.item.path)}}},[e._m(0),e._v(" "),n("div",{staticClass:"media-browser-item-info"},[e._v("\n        "+e._s(e.item.name)+"\n    ")])])},r.staticRenderFns=[function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"media-browser-item-preview"},[n("span",{staticClass:"icon material-icons"},[e._v("folder")])])}],t.hot&&!function(){var n=e("vue-hot-reload-api");n.install(e("vue"),!0),n.compatible&&(t.hot.accept(),t.hot.data?n.reload("data-v-6b786564",r):n.createRecord("data-v-6b786564",r))}()},{vue:4,"vue-hot-reload-api":3}],11:[function(e,t,n){!function(){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default={name:"media-browser-item-file",props:["item"]}}(),t.exports.__esModule&&(t.exports=t.exports.default);var r="function"==typeof t.exports?t.exports.options:t.exports;r.functional&&console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions."),r.render=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"media-browser-item-file"},[e._m(0),e._v(" "),n("div",{staticClass:"media-browser-item-info"},[e._v(e._s(e.item.name))])])},r.staticRenderFns=[function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"media-browser-item-preview"},[n("span",{staticClass:"icon material-icons"},[e._v("insert_drive_file")])])}],t.hot&&!function(){var n=e("vue-hot-reload-api");n.install(e("vue"),!0),n.compatible&&(t.hot.accept(),t.hot.data?n.reload("data-v-222b94aa",r):n.createRecord("data-v-222b94aa",r))}()},{vue:4,"vue-hot-reload-api":3}],12:[function(e,t,n){!function(){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default={name:"media-browser-item-image",props:["item"]}}(),t.exports.__esModule&&(t.exports=t.exports.default);var r="function"==typeof t.exports?t.exports.options:t.exports;r.functional&&console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions."),r.render=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"media-browser-image"},[n("div",{staticClass:"media-browser-item-preview"},[n("div",{staticClass:"image-brackground"},[n("div",{staticClass:"image-cropped",style:{backgroundImage:"url(/images"+e.item.path+")"}})])]),e._v(" "),n("div",{staticClass:"media-browser-item-info"},[e._v("\n        "+e._s(e.item.name)+"\n    ")])])},r.staticRenderFns=[],t.hot&&!function(){var n=e("vue-hot-reload-api");n.install(e("vue"),!0),n.compatible&&(t.hot.accept(),t.hot.data?n.reload("data-v-0b19fdbc",r):n.createRecord("data-v-0b19fdbc",r))}()},{vue:4,"vue-hot-reload-api":3}],13:[function(e,t,n){"use strict";function r(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(n,"__esModule",{value:!0});var o=e("./directory.vue"),i=r(o),a=e("./file.vue"),s=r(a),c=e("./image.vue"),u=r(c);n.default={functional:!0,props:["item"],render:function(e,t){function n(){var e=t.props.item,n=["jpg","png","gif"];return"dir"===e.type?i.default:e.extension&&n.indexOf(e.extension.toLowerCase())!==-1?u.default:s.default}return e("div",{class:"media-browser-item"},[e(n(),{props:t.props})])}}},{"./directory.vue":10,"./file.vue":11,"./image.vue":12}],14:[function(e,t,n){!function(){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default={name:"media-dnd",props:["content"]};var e=function(e){var t=new FileReader;t.onloadend=function(n){UploadFile(e.name,t.result)},t.readAsDataURL(e)};document.addEventListener("dragleave",function(e){return e.stopPropagation(),e.preventDefault(),document.querySelector(".media-browser").style.borderWidth="1px",document.querySelector(".media-browser").style.borderStyle="solid",!1}),window.UploadFile=function(e,t){var n={name:e,content:t.replace(/data:+.+base64,/,"")},r="",o=new XMLHttpRequest;o.upload.onprogress=function(e){var t=e.loaded/e.total*100;document.getElementById("progress-bar-com-media-tmp").style.width=t+"%"};var i=function(){setTimeout(function(){document.querySelector("#jloader").outerHTML="",delete document.querySelector("#jloader"),document.querySelector(".media-browser").style.borderWidth="1px",document.querySelector(".media-browser").style.borderStyle="solid"},200)};o.onload=function(){try{var e=JSON.parse(o.responseText)}catch(e){var e=null}e?200==o.status&&(1==e.success&&i(),"1"==e.status&&(Joomla.renderMessages({success:[e.message]},"true"),i())):i()},o.onerror=function(){i()},o.open("POST","/administrator/index.php?option=com_media&task=api.files&format=json&path=/"+r+"/"+e,!0),o.setRequestHeader("Content-Type","application/json"),o.send(JSON.stringify(n))},document.addEventListener("dragenter",function(e){return e.stopPropagation(),!1}),document.addEventListener("dragover",function(e){return e.preventDefault(),document.querySelector(".media-browser").style.borderStyle="dashed",document.querySelector(".media-browser").style.borderWidth="5px",!1}),document.addEventListener("drop",function(t){if(t.preventDefault(),t.dataTransfer&&t.dataTransfer.files&&t.dataTransfer.files.length>0)for(var n,r=0;n=t.dataTransfer.files[r];r++)n.name.toLowerCase().match(/\.(jpg|jpeg|png|gif)$/)&&(document.querySelector(".media-browser").insertAdjacentHTML("afterbegin",'<div id="jloader">   <div class="progress progress-success progress-striped active" style="width:100%;height:30px;">       <div id="progress-bar-com-media-tmp" class="bar" style="width: 0%"></div>   </div></div>'),document.querySelector(".media-browser").style.borderWidth="1px",document.querySelector(".media-browser").style.borderStyle="solid"),t.preventDefault(),e(n);document.querySelector(".media-browser").style.borderWidth="1px",document.querySelector(".media-browser").style.borderStyle="solid"})}(),t.exports.__esModule&&(t.exports=t.exports.default);var r="function"==typeof t.exports?t.exports.options:t.exports;r.functional&&console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions."),r.render=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div")},r.staticRenderFns=[],t.hot&&!function(){var n=e("vue-hot-reload-api");n.install(e("vue"),!0),n.compatible&&(t.hot.accept(),t.hot.data?n.reload("data-v-621ac674",r):n.createRecord("data-v-621ac674",r))}()},{vue:4,"vue-hot-reload-api":3}],15:[function(e,t,n){!function(){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default={name:"media-toolbar"}}(),t.exports.__esModule&&(t.exports=t.exports.default);var r="function"==typeof t.exports?t.exports.options:t.exports;r.functional&&console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions."),r.render=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"media-toolbar"},[e._m(0),e._v(" "),n("media-breadcrumb"),e._v(" "),e._m(1)],1)},r.staticRenderFns=[function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"create-wrapper"},[n("div",{staticClass:"btn-group"},[n("button",{staticClass:"btn btn-success"},[e._v("Upload")]),e._v(" "),n("button",{staticClass:"btn dropdown-toggle btn-success",attrs:{"data-toggle":"dropdown"}},[n("span",{staticClass:"caret"})]),e._v(" "),n("ul",{staticClass:"dropdown-menu"},[n("li",[n("a",{attrs:{href:"#"}},[e._v("Create Folder")])]),e._v(" "),n("li",{staticClass:"divider"}),e._v(" "),n("li",[n("a",{attrs:{href:"#"}},[e._v("Upload File")])]),e._v(" "),n("li",[n("a",{attrs:{href:"#"}},[e._v("Upload Folder")])])])])])},function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"media-tools"},[n("a",{staticClass:"material-icons",attrs:{href:"#"}},[e._v("list")]),e._v(" "),n("a",{staticClass:"material-icons",attrs:{href:"#"}},[e._v("info_outline")]),e._v(" "),n("a",{staticClass:"material-icons",attrs:{href:"#"}},[e._v("help_outline")]),e._v(" "),n("a",{staticClass:"material-icons",attrs:{href:"#"}},[e._v("settings")])])}],t.hot&&!function(){var n=e("vue-hot-reload-api");n.install(e("vue"),!0),n.compatible&&(t.hot.accept(),t.hot.data?n.reload("data-v-27bf0984",r):n.createRecord("data-v-27bf0984",r))}()},{vue:4,"vue-hot-reload-api":3}],16:[function(e,t,n){!function(){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default={name:"media-tree-item",props:["item"],computed:{isActive:function(){return this.item.path===this.$store.state.selectedDirectory},isOpen:function(){return this.$store.state.selectedDirectory.includes(this.item.path)},level:function(){return this.item.path.split("/").length-1},hasChildren:function(){return this.item.directories.length>0}},methods:{toggleItem:function(){this.$store.dispatch("getContents",this.item.path)}}}}(),t.exports.__esModule&&(t.exports=t.exports.default);var r="function"==typeof t.exports?t.exports.options:t.exports;r.functional&&console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions."),r.render=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("li",{staticClass:"media-tree-item",class:{active:e.isActive}},[n("a",{style:{paddingLeft:15*e.level+"px"},on:{click:function(t){t.stopPropagation(),t.preventDefault(),e.toggleItem()}}},[n("span",{staticClass:"item-icon material-icons"},[e._v("folder")]),e._v(" "),n("span",{staticClass:"item-name"},[e._v(e._s(e.item.name))])]),e._v(" "),n("transition",{attrs:{name:"slide-fade"}},[e.hasChildren?n("media-tree",{directives:[{name:"show",rawName:"v-show",value:e.isOpen,expression:"isOpen"}],attrs:{root:e.item.path}}):e._e()],1)],1)},r.staticRenderFns=[],t.hot&&!function(){var n=e("vue-hot-reload-api");n.install(e("vue"),!0),n.compatible&&(t.hot.accept(),t.hot.data?n.reload("data-v-255491fb",r):n.createRecord("data-v-255491fb",r))}()},{vue:4,"vue-hot-reload-api":3}],17:[function(e,t,n){!function(){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default={name:"media-tree",props:["root"],computed:{directories:function(){var e=this;return this.$store.state.directories.filter(function(t){return t.directory===e.root})}}}}(),t.exports.__esModule&&(t.exports=t.exports.default);var r="function"==typeof t.exports?t.exports.options:t.exports;r.functional&&console.error("[vueify] functional components are not supported and should be defined in plain js files using render functions."),r.render=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("ul",{staticClass:"media-tree"},e._l(e.directories,function(e){return n("media-tree-item",{attrs:{item:e}})}))},r.staticRenderFns=[],t.hot&&!function(){var n=e("vue-hot-reload-api");n.install(e("vue"),!0),n.compatible&&(t.hot.accept(),t.hot.data?n.reload("data-v-31c44d06",r):n.createRecord("data-v-31c44d06",r))}()},{vue:4,"vue-hot-reload-api":3}],18:[function(e,t,n){"use strict";function r(e){return e&&e.__esModule?e:{default:e}}var o=e("vue"),i=r(o),a=e("./components/app.vue"),s=r(a),c=e("./components/tree/tree.vue"),u=r(c),l=e("./components/tree/item.vue"),d=r(l),f=e("./components/toolbar/toolbar.vue"),p=r(f),v=e("./components/breadcrumb/breadcrumb.vue"),h=r(v),m=e("./components/browser/browser.vue"),y=r(m),_=e("./components/browser/items/item"),g=r(_),b=e("./store/store"),w=r(b),x=e("./components/draganddrop/dnd.vue");r(x);i.default.component("media-tree",u.default),i.default.component("media-tree-item",d.default),i.default.component("media-toolbar",p.default),i.default.component("media-breadcrumb",h.default),i.default.component("media-browser",y.default),i.default.component("media-browser-item",g.default),document.addEventListener("DOMContentLoaded",function(e){return new i.default({el:"#com-media",store:w.default,render:function(e){return e(s.default)}})})},{"./components/app.vue":7,"./components/breadcrumb/breadcrumb.vue":8,"./components/browser/browser.vue":9,"./components/browser/items/item":13,"./components/draganddrop/dnd.vue":14,"./components/toolbar/toolbar.vue":15,"./components/tree/item.vue":16,"./components/tree/tree.vue":17,"./store/store":24,vue:4}],19:[function(e,t,n){"use strict";function r(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&(t[n]=e[n]);return t.default=e,t}Object.defineProperty(n,"__esModule",{value:!0}),n.getContents=void 0;var o=e("../app/Api"),i=e("./mutation-types"),a=r(i);n.getContents=function(e,t){var n=e.commit;o.api.getContents(t).then(function(e){n(a.LOAD_CONTENTS_SUCCESS,e),
n(a.SELECT_DIRECTORY,t)}).catch(function(e){console.log("error",e)})}},{"../app/Api":6,"./mutation-types":21}],20:[function(e,t,n){"use strict";function r(e){if(Array.isArray(e)){for(var t=0,n=Array(e.length);t<e.length;t++)n[t]=e[t];return n}return Array.from(e)}Object.defineProperty(n,"__esModule",{value:!0});n.getSelectedDirectory=function(e){return e.directories.find(function(t){return t.path===e.selectedDirectory})},n.getSelectedDirectoryDirectories=function(e,t){return t.getSelectedDirectory.directories.map(function(t){return e.directories.find(function(e){return e.path===t})})},n.getSelectedDirectoryFiles=function(e,t){return t.getSelectedDirectory.files.map(function(t){return e.files.find(function(e){return e.path===t})})},n.getSelectedDirectoryContents=function(e,t){return[].concat(r(t.getSelectedDirectoryDirectories),r(t.getSelectedDirectoryFiles))}},{}],21:[function(e,t,n){"use strict";Object.defineProperty(n,"__esModule",{value:!0});n.SELECT_DIRECTORY="SELECT_DIRECTORY",n.LOAD_CONTENTS_SUCCESS="LOAD_CONTENTS_SUCCESS"},{}],22:[function(e,t,n){"use strict";function r(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&(t[n]=e[n]);return t.default=e,t}function o(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function i(e){if(Array.isArray(e)){for(var t=0,n=Array(e.length);t<e.length;t++)n[t]=e[t];return n}return Array.from(e)}Object.defineProperty(n,"__esModule",{value:!0});var a,s=e("./mutation-types"),c=r(s);n.default=(a={},o(a,c.SELECT_DIRECTORY,function(e,t){e.selectedDirectory=t}),o(a,c.LOAD_CONTENTS_SUCCESS,function(e,t){var n=t.directories.filter(function(t){return!e.directories.some(function(e){return e.path===t.path})}),r=t.files.filter(function(t){return!e.files.some(function(e){return e.path===t.path})});if(n.length>0){var o,a=n.map(function(e){return e.path}),s=e.directories.find(function(e){return e.path===n[0].directory}),c=e.directories.indexOf(s);(o=e.directories).push.apply(o,i(n)),e.directories.splice(c,1,Object.assign({},s,{directories:[].concat(i(s.directories),i(a))}))}if(r.length>0){var u,l=r.map(function(e){return e.path}),d=e.directories.find(function(e){return e.path===r[0].directory}),f=e.directories.indexOf(d);(u=e.files).push.apply(u,i(r)),e.directories.splice(f,1,Object.assign({},d,{files:[].concat(i(d.files),i(l))}))}}),a)},{"./mutation-types":21}],23:[function(e,t,n){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default={selectedDirectory:"/",directories:[{path:"/",directories:[],files:[],directory:null}],files:[]}},{}],24:[function(e,t,n){"use strict";function r(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&(t[n]=e[n]);return t.default=e,t}function o(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(n,"__esModule",{value:!0});var i=e("vue"),a=o(i),s=e("vuex"),c=o(s),u=e("./state"),l=o(u),d=e("./getters"),f=r(d),p=e("./actions"),v=r(p),h=e("./mutations"),m=o(h);a.default.use(c.default),n.default=new c.default.Store({state:l.default,getters:f,actions:v,mutations:m.default,strict:!0})},{"./actions":19,"./getters":20,"./mutations":22,"./state":23,vue:4,vuex:5}]},{},[18]);
>>>>>>> Init
