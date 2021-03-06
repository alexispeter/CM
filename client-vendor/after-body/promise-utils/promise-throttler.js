/*
 * Author: CM
 */

(function(global) {

  /**
   * @callback PromiseThrottled
   * @param {*} any number of optional params
   * @return {Promise}
   */

  /**
   * @param {PromiseThrottled} fn
   * @param {Object|null} [options]
   * @param {Boolean} options.cancelLeading Whether to cancel the previous promise if it is still running.
   * @param {Boolean} options.queue Whether to queue the next promises.
   * @param {String} options.key A custom key to store the resulted PromiseThrottled.
   * @returns {PromiseThrottled}
   */
  function promiseThrottler(fn, options) {
    options = _.defaults(
      options || {}, {
        cancelLeading: false,
        queue: false
      }
    );
    if ((+options.cancelLeading) + (+options.queue) > 1) {
      throw new Error('PromiseThrottler options "cancelLeading", "queue" exclude each other.');
    }
    if (options.key) {
      return namespaceThrottler(options.key, fn, options);
    } else {
      return nonameThrottler(fn, options);
    }
  }

  /**
   * @see promiseThrottler
   */
  function nonameThrottler(fn, options) {
    var promise;

    return function() {
      if (options.cancelLeading && promise && promise.isPending() && promise.isCancellable()) {
        promise.cancel();
        promise = null;
      }
      if (options.queue && promise && promise.isPending()) {
        var args = arguments;
        var self = this;
        promise = promise.finally(function() {
          return fn.apply(self, args);
        });
      }
      if (!promise || !promise.isPending()) {
        promise = fn.apply(this, arguments);
      }
      return promise;
    };
  }

  var storage = {};

  /**
   * @see promiseThrottler
   */
  function namespaceThrottler(namespace, fn, options) {
    if (!storage[namespace]) {
      storage[namespace] = nonameThrottler(fn, options);
    }
    return storage[namespace];
  }

  /**
   * @param {String} namespace
   */
  function removeThrottler(namespace) {
    if (storage[namespace]) {
      delete storage[namespace];
    }
  }

  global.promiseThrottler = promiseThrottler;
  global.removePromiseThrottler = removeThrottler;

})(window);
