/*! jQuery Validation Plugin - v1.11.0 - 2/4/2013
 https://github.com/jzaefferer/jquery-validation
 Copyright (c) 2013 Jörn Zaefferer; Licensed MIT */
(function($) {
  $.extend($.fn, {
    /**
     * @param {Object} options
     * @return {?}
     */
    validate : function(options) {
      if (!this.length) {
        if (options && (options.debug && window.console)) {
          console.warn("Nothing selected, can't validate, returning nothing.");
        }
        return;
      }
      var validator = $.data(this[0], "validator");
      if (validator) {
        return validator;
      }
      this.attr("novalidate", "novalidate");
      validator = new $.validator(options, this[0]);
      $.data(this[0], "validator", validator);
      if (validator.settings.onsubmit) {
        this.validateDelegate(":submit", "click", function(event) {
          if (validator.settings.submitHandler) {
            validator.submitButton = event.target;
          }
          if ($(event.target).hasClass("cancel")) {
            /** @type {boolean} */
            validator.cancelSubmit = true;
          }
        });
        this.submit(function(types) {
          /**
           * @return {?}
           */
          function handle() {
            var selfObj;
            if (validator.settings.submitHandler) {
              if (validator.submitButton) {
                selfObj = $("<input type='hidden'/>").attr("name", validator.submitButton.name).val(validator.submitButton.value).appendTo(validator.currentForm);
              }
              validator.settings.submitHandler.call(validator, validator.currentForm, types);
              if (validator.submitButton) {
                selfObj.remove();
              }
              return false;
            }
            return true;
          }
          if (validator.settings.debug) {
            types.preventDefault();
          }
          if (validator.cancelSubmit) {
            /** @type {boolean} */
            validator.cancelSubmit = false;
            return handle();
          }
          if (validator.form()) {
            if (validator.pendingRequest) {
              /** @type {boolean} */
              validator.formSubmitted = true;
              return false;
            }
            return handle();
          } else {
            validator.focusInvalid();
            return false;
          }
        });
      }
      return validator;
    },
    /**
     * @return {?}
     */
    valid : function() {
      if ($(this[0]).is("form")) {
        return this.validate().form();
      } else {
        /** @type {boolean} */
        var valid = true;
        var validator = $(this[0].form).validate();
        this.each(function() {
          valid &= validator.element(this);
        });
        return valid;
      }
    },
    /**
     * @param {string} attributes
     * @return {?}
     */
    removeAttrs : function(attributes) {
      var result = {};
      var $element = this;
      $.each(attributes.split(/\s/), function(dataAndEvents, value) {
        result[value] = $element.attr(value);
        $element.removeAttr(value);
      });
      return result;
    },
    /**
     * @param {?} command
     * @param {string} argument
     * @return {?}
     */
    rules : function(command, argument) {
      var element = this[0];
      if (command) {
        var settings = $.data(element.form, "validator").settings;
        var staticRules = settings.rules;
        var existingRules = $.validator.staticRules(element);
        switch(command) {
          case "add":
            $.extend(existingRules, $.validator.normalizeRule(argument));
            staticRules[element.name] = existingRules;
            if (argument.messages) {
              settings.messages[element.name] = $.extend(settings.messages[element.name], argument.messages);
            }
            break;
          case "remove":
            if (!argument) {
              delete staticRules[element.name];
              return existingRules;
            }
            var filtered = {};
            $.each(argument.split(/\s/), function(dataAndEvents, method) {
              filtered[method] = existingRules[method];
              delete existingRules[method];
            });
            return filtered;
        }
      }
      var data = $.validator.normalizeRules($.extend({}, $.validator.classRules(element), $.validator.attributeRules(element), $.validator.dataRules(element), $.validator.staticRules(element)), element);
      if (data.required) {
        var param = data.required;
        delete data.required;
        data = $.extend({
          required : param
        }, data);
      }
      return data;
    }
  });
  $.extend($.expr[":"], {
    /**
     * @param {Attr} a
     * @return {?}
     */
    blank : function(a) {
      return!$.trim("" + a.value);
    },
    /**
     * @param {Attr} a
     * @return {?}
     */
    filled : function(a) {
      return!!$.trim("" + a.value);
    },
    /**
     * @param {Element} a
     * @return {?}
     */
    unchecked : function(a) {
      return!a.checked;
    }
  });
  /**
   * @param {?} options
   * @param {?} form
   * @return {undefined}
   */
  $.validator = function(options, form) {
    this.settings = $.extend(true, {}, $.validator.defaults, options);
    this.currentForm = form;
    this.init();
  };
  /**
   * @param {string} source
   * @param {(Array|string)} params
   * @return {?}
   */
  $.validator.format = function(source, params) {
    if (arguments.length === 1) {
      return function() {
        var args = $.makeArray(arguments);
        args.unshift(source);
        return $.validator.format.apply(this, args);
      };
    }
    if (arguments.length > 2 && params.constructor !== Array) {
      params = $.makeArray(arguments).slice(1);
    }
    if (params.constructor !== Array) {
      /** @type {Array} */
      params = [params];
    }
    $.each(params, function(dataAndEvents, deepDataAndEvents) {
      source = source.replace(new RegExp("\\{" + dataAndEvents + "\\}", "g"), function() {
        return deepDataAndEvents;
      });
    });
    return source;
  };
  $.extend($.validator, {
    defaults : {
      messages : {},
      groups : {},
      rules : {},
      errorClass : "invalid",
      validClass : "valid",
      errorElement : "em",
      focusInvalid : true,
      errorContainer : $([]),
      errorLabelContainer : $([]),
      onsubmit : true,
      ignore : ":hidden",
      ignoreTitle : false,
      /**
       * @param {(Object|string)} element
       * @param {?} event
       * @return {undefined}
       */
      onfocusin : function(element, event) {
        /** @type {(Object|string)} */
        this.lastActive = element;
        if (this.settings.focusCleanup && !this.blockFocusCleanup) {
          if (this.settings.unhighlight) {
            this.settings.unhighlight.call(this, element, this.settings.errorClass, this.settings.validClass);
          }
          this.addWrapper(this.errorsFor(element)).hide();
        }
      },
      /**
       * @param {Object} element
       * @param {?} event
       * @return {undefined}
       */
      onfocusout : function(element, event) {
        if (!this.checkable(element) && (element.name in this.submitted || !this.optional(element))) {
          this.element(element);
        }
      },
      /**
       * @param {Function} element
       * @param {Event} event
       * @return {undefined}
       */
      onkeyup : function(element, event) {
        if (event.which === 9 && this.elementValue(element) === "") {
          return;
        } else {
          if (element.name in this.submitted || element === this.lastElement) {
            this.element(element);
          }
        }
      },
      /**
       * @param {Object} element
       * @param {?} evt
       * @return {undefined}
       */
      onclick : function(element, evt) {
        if (element.name in this.submitted) {
          this.element(element);
        } else {
          if (element.parentNode.name in this.submitted) {
            this.element(element.parentNode);
          }
        }
      },
      /**
       * @param {Object} element
       * @param {?} errorClass
       * @param {?} validClass
       * @return {undefined}
       */
      highlight : function(element, errorClass, validClass) {
        if (element.type === "radio") {
          this.findByName(element.name).addClass(errorClass).removeClass(validClass).parent().addClass("state-error").removeClass("state-success");
        } else {
          $(element).addClass(errorClass).removeClass(validClass).parent().addClass("state-error").removeClass("state-success");
        }
      },
      /**
       * @param {Object} element
       * @param {?} errorClass
       * @param {?} validClass
       * @return {undefined}
       */
      unhighlight : function(element, errorClass, validClass) {
        if (element.type === "radio") {
          this.findByName(element.name).removeClass(errorClass).addClass(validClass).parent().addClass("state-success").removeClass("state-error");
        } else {
          $(element).removeClass(errorClass).addClass(validClass).parent().addClass("state-success").removeClass("state-error");
        }
      }
    },
    /**
     * @param {?} settings
     * @return {undefined}
     */
    setDefaults : function(settings) {
      $.extend($.validator.defaults, settings);
    },
    messages : {
      required : "Este campo es requerido",
      remote : "Corrija este campo",
      email : "Ingrese un email válido",
      url : "Ingrese una URL válida",
      date : "Ingrese una fecha válida",
      dateISO : "Ingrese una fecha válida (ISO)",
      number : "Ingrese un número válido",
      digits : "Ingrese solo digitos",
      creditcard : "Ingrese un número de tarjeta de crédito",
      equalTo : "Ingrese el mismo valor de nuevo",
      maxlength : $.validator.format("Ingrese un máximo de {0} caracteres"),
      minlength : $.validator.format("Ingrese un minimo de {0} caracteres"),
      rangelength : $.validator.format("Ingrese un valor entre {0} y {1} caracteres de longitud"),
      range : $.validator.format("Ingrese un valor entre {0} y {1}"),
      max : $.validator.format("Ingrese un valor menor o igual a {0}"),
      min : $.validator.format("Ingrese un valor mayor o igual a {0}"),
      regular: "Ingrese catacteres válidos",
      decimal: "Ingrese un número decimal válido",
      integer: "Ingrese número entero válido"
    },
    autoCreateRanges : false,
    prototype : {
      /**
       * @return {undefined}
       */
      init : function() {
        /**
         * @param {Event} event
         * @return {undefined}
         */
        function delegate(event) {
          var validator = $.data(this[0].form, "validator");
          var eventType = "on" + event.type.replace(/^validate/, "");
          if (validator.settings[eventType]) {
            validator.settings[eventType].call(validator, this[0], event);
          }
        }
        this.labelContainer = $(this.settings.errorLabelContainer);
        this.errorContext = this.labelContainer.length && this.labelContainer || $(this.currentForm);
        this.containers = $(this.settings.errorContainer).add(this.settings.errorLabelContainer);
        this.submitted = {};
        this.valueCache = {};
        /** @type {number} */
        this.pendingRequest = 0;
        this.pending = {};
        this.invalid = {};
        this.reset();
        var benchmarks = this.groups = {};
        $.each(this.settings.groups, function(ref, types) {
          if (typeof types === "string") {
            /** @type {Array.<string>} */
            types = types.split(/\s/);
          }
          $.each(types, function(dataAndEvents, name) {
            benchmarks[name] = ref;
          });
        });
        var rules = this.settings.rules;
        $.each(rules, function(key, value) {
          rules[key] = $.validator.normalizeRule(value);
        });
        $(this.currentForm).validateDelegate(":text, [type='password'], [type='file'], select, textarea, " + "[type='number'], [type='search'] ,[type='tel'], [type='url'], " + "[type='email'], [type='datetime'], [type='date'], [type='month'], " + "[type='week'], [type='time'], [type='datetime-local'], " + "[type='range'], [type='color'] ", "focusin focusout keyup", delegate).validateDelegate("[type='radio'], [type='checkbox'], select, option", "click", delegate);
        if (this.settings.invalidHandler) {
          $(this.currentForm).bind("invalid-form.validate", this.settings.invalidHandler);
        }
      },
      /**
       * @return {?}
       */
      form : function() {
        this.checkForm();
        $.extend(this.submitted, this.errorMap);
        this.invalid = $.extend({}, this.errorMap);
        if (!this.valid()) {
          $(this.currentForm).triggerHandler("invalid-form", [this]);
        }
        this.showErrors();
        return this.valid();
      },
      /**
       * @return {?}
       */
      checkForm: function() {
        this.prepareForm();
        for ( var i = 0, elements = (this.currentElements = this.elements()); elements[i]; i++ ) {
        if (this.findByName( elements[i].name ).length != undefined && this.findByName( elements[i].name ).length > 1) {
        for (var cnt = 0; cnt < this.findByName( elements[i].name ).length; cnt++) {
        this.check( this.findByName( elements[i].name )[cnt] );
        }
        } else {
        this.check( elements[i] );
        }
        }
        return this.valid();
     },
      /**
       * @param {Function} element
       * @return {?}
       */
      element : function(element) {
        element = this.validationTargetFor(this.clean(element));
        /** @type {Function} */
        this.lastElement = element;
        this.prepareElement(element);
        this.currentElements = $(element);
        /** @type {boolean} */
        var result = this.check(element) !== false;
        if (result) {
          delete this.invalid[element.name];
        } else {
          /** @type {boolean} */
          this.invalid[element.name] = true;
        }
        if (!this.numberOfInvalids()) {
          this.toHide = this.toHide.add(this.containers);
        }
        this.showErrors();
        return result;
      },
      /**
       * @param {Object} errors
       * @return {undefined}
       */
      showErrors : function(errors) {
        if (errors) {
          $.extend(this.errorMap, errors);
          /** @type {Array} */
          this.errorList = [];
          var name;
          for (name in errors) {
            this.errorList.push({
              message : errors[name],
              element : this.findByName(name)[0]
            });
          }
          this.successList = $.grep(this.successList, function(element) {
            return!(element.name in errors);
          });
        }
        if (this.settings.showErrors) {
          this.settings.showErrors.call(this, this.errorMap, this.errorList);
        } else {
          this.defaultShowErrors();
        }
      },
      /**
       * @return {undefined}
       */
      resetForm : function() {
        if ($.fn.resetForm) {
          $(this.currentForm).resetForm();
        }
        this.submitted = {};
        /** @type {null} */
        this.lastElement = null;
        this.prepareForm();
        this.hideErrors();
        this.elements().removeClass(this.settings.errorClass).removeData("previousValue");
      },
      /**
       * @return {?}
       */
      numberOfInvalids : function() {
        return this.objectLength(this.invalid);
      },
      /**
       * @param {?} obj
       * @return {?}
       */
      objectLength : function(obj) {
        /** @type {number} */
        var count = 0;
        var prop;
        for (prop in obj) {
          count++;
        }
        return count;
      },
      /**
       * @return {undefined}
       */
      hideErrors : function() {
        this.addWrapper(this.toHide).hide();
      },
      /**
       * @return {?}
       */
      valid : function() {
        return this.size() === 0;
      },
      /**
       * @return {?}
       */
      size : function() {
        return this.errorList.length;
      },
      /**
       * @return {undefined}
       */
      focusInvalid : function() {
        if (this.settings.focusInvalid) {
          try {
            $(this.findLastActive() || (this.errorList.length && this.errorList[0].element || [])).filter(":visible").focus().trigger("focusin");
          } catch (e) {
          }
        }
      },
      /**
       * @return {?}
       */
      findLastActive : function() {
        var lastActive = this.lastActive;
        return lastActive && ($.grep(this.errorList, function(n) {
          return n.element.name === lastActive.name;
        }).length === 1 && lastActive);
      },
      /**
       * @return {?}
       */
      elements : function() {
        var validator = this;
        var rulesCache = {};
        return $(this.currentForm).find("input, select, textarea").not(":submit, :reset, :image, [disabled]").not(this.settings.ignore).filter(function() {
          if (!this.name && (validator.settings.debug && window.console)) {
            console.error("%o has no name assigned", this);
          }
          if (this.name in rulesCache || !validator.objectLength($(this).rules())) {
            return false;
          }
          /** @type {boolean} */
          rulesCache[this.name] = true;
          return true;
        });
      },
      /**
       * @param {Function} selector
       * @return {?}
       */
      clean : function(selector) {
        return $(selector)[0];
      },
      /**
       * @return {?}
       */
      errors : function() {
        var errorClass = this.settings.errorClass.replace(" ", ".");
        return $(this.settings.errorElement + "." + errorClass, this.errorContext);
      },
      /**
       * @return {undefined}
       */
      reset : function() {
        /** @type {Array} */
        this.successList = [];
        /** @type {Array} */
        this.errorList = [];
        this.errorMap = {};
        this.toShow = $([]);
        this.toHide = $([]);
        this.currentElements = $([]);
      },
      /**
       * @return {undefined}
       */
      prepareForm : function() {
        this.reset();
        this.toHide = this.errors().add(this.containers);
      },
      /**
       * @param {Object} element
       * @return {undefined}
       */
      prepareElement : function(element) {
        this.reset();
        this.toHide = this.errorsFor(element);
      },
      /**
       * @param {Function} element
       * @return {?}
       */
      elementValue : function(element) {
        var type = $(element).attr("type");
        var val = $(element).val();
        if (type === "radio" || type === "checkbox") {
          return $("input[name='" + $(element).attr("name") + "']:checked").val();
        }
        if (typeof val === "string") {
          return val.replace(/\r/g, "");
        }
        return val;
      },
      /**
       * @param {Object} element
       * @return {?}
       */
      check : function(element) {
        element = this.validationTargetFor(this.clean(element));
        var rules = $(element).rules();
        /** @type {boolean} */
        var dependencyMismatch = false;
        var val = this.elementValue(element);
        var result;
        var method;
        for (method in rules) {
          var rule = {
            method : method,
            parameters : rules[method]
          };
          try {
            result = $.validator.methods[method].call(this, val, element, rule.parameters);
            if (result === "dependency-mismatch") {
              /** @type {boolean} */
              dependencyMismatch = true;
              continue;
            }
            /** @type {boolean} */
            dependencyMismatch = false;
            if (result === "pending") {
              this.toHide = this.toHide.not(this.errorsFor(element));
              return;
            }
            if (!result) {
              this.formatAndAdd(element, rule);
              return false;
            }
          } catch (matches) {
            if (this.settings.debug && window.console) {
              console.log("Exception occured when checking element " + element.id + ", check the '" + rule.method + "' method.", matches);
            }
            throw matches;
          }
        }
        if (dependencyMismatch) {
          return;
        }
        if (this.objectLength(rules)) {
          this.successList.push(element);
        }
        return true;
      },
      /**
       * @param {Object} element
       * @param {string} method
       * @return {?}
       */
      customDataMessage : function(element, method) {
        return $(element).data("msg-" + method.toLowerCase()) || element.attributes && $(element).attr("data-msg-" + method.toLowerCase());
      },
      /**
       * @param {?} name
       * @param {string} method
       * @return {?}
       */
      customMessage : function(name, method) {
        var m = this.settings.messages[name];
        return m && (m.constructor === String ? m : m[method]);
      },
      /**
       * @return {?}
       */
      findDefined : function() {
        /** @type {number} */
        var i = 0;
        for (;i < arguments.length;i++) {
          if (arguments[i] !== undefined) {
            return arguments[i];
          }
        }
        return undefined;
      },
      /**
       * @param {Object} element
       * @param {string} method
       * @return {?}
       */
      defaultMessage : function(element, method) {
        return this.findDefined(this.customMessage(element.name, method), this.customDataMessage(element, method), !this.settings.ignoreTitle && element.title || undefined, $.validator.messages[method], "<strong>Warning: No message defined for " + element.name + "</strong>");
      },
      /**
       * @param {Object} element
       * @param {Object} rule
       * @return {undefined}
       */
      formatAndAdd : function(element, rule) {
        var message = this.defaultMessage(element, rule.method);
        /** @type {RegExp} */
        var theregex = /\$?\{(\d+)\}/g;
        if (typeof message === "function") {
          message = message.call(this, rule.parameters, element);
        } else {
          if (theregex.test(message)) {
            message = $.validator.format(message.replace(theregex, "{$1}"), rule.parameters);
          }
        }
        this.errorList.push({
          message : message,
          element : element
        });
        this.errorMap[element.name] = message;
        this.submitted[element.name] = message;
      },
      /**
       * @param {Object} toToggle
       * @return {?}
       */
      addWrapper : function(toToggle) {
        if (this.settings.wrapper) {
          toToggle = toToggle.add(toToggle.parent(this.settings.wrapper));
        }
        return toToggle;
      },
      /**
       * @return {undefined}
       */
      defaultShowErrors : function() {
        var i;
        var arr2;
        /** @type {number} */
        i = 0;
        for (;this.errorList[i];i++) {
          var error = this.errorList[i];
          if (this.settings.highlight) {
            this.settings.highlight.call(this, error.element, this.settings.errorClass, this.settings.validClass);
          }
          this.showLabel(error.element, error.message);
        }
        if (this.errorList.length) {
          this.toShow = this.toShow.add(this.containers);
        }
        if (this.settings.success) {
          /** @type {number} */
          i = 0;
          for (;this.successList[i];i++) {
            this.showLabel(this.successList[i]);
          }
        }
        if (this.settings.unhighlight) {
          /** @type {number} */
          i = 0;
          arr2 = this.validElements();
          for (;arr2[i];i++) {
            this.settings.unhighlight.call(this, arr2[i], this.settings.errorClass, this.settings.validClass);
          }
        }
        this.toHide = this.toHide.not(this.toShow);
        this.hideErrors();
        this.addWrapper(this.toShow).show();
      },
      /**
       * @return {?}
       */
      validElements : function() {
        return this.currentElements.not(this.invalidElements());
      },
      /**
       * @return {?}
       */
      invalidElements : function() {
        return $(this.errorList).map(function() {
          return this.element;
        });
      },
      /**
       * @param {Object} element
       * @param {string} message
       * @return {undefined}
       */
      showLabel : function(element, message) {
        var label = this.errorsFor(element);
        if (label.length) {
          label.removeClass(this.settings.validClass).addClass(this.settings.errorClass);
          label.html(message);
        } else {
          label = $("<" + this.settings.errorElement + ">").attr("for", this.idOrName(element)).addClass(this.settings.errorClass).html(message || "");
          label.css({
              color: '#990000',
              padding: '5px'
          });
          if (this.settings.wrapper) {
            label = label.hide().show().wrap("<" + this.settings.wrapper + "/>").parent();
          }
          if (!this.labelContainer.append(label).length) {
            if (this.settings.errorPlacement) {
              this.settings.errorPlacement(label, $(element));
            } else {
              label.insertAfter(element);
            }
          }
        }
        if (!message && this.settings.success) {
          label.text("");
          if (typeof this.settings.success === "string") {
            label.addClass(this.settings.success);
          } else {
            this.settings.success(label, element);
          }
        }
        this.toShow = this.toShow.add(label);/*RDCC .delay(5000).fadeOut()*/
      },
      /**
       * @param {Object} element
       * @return {?}
       */
      errorsFor : function(element) {
        var name = this.idOrName(element);
        return this.errors().filter(function() {
          return $(this).attr("for") === name;
        });
      },
      /**
       * @param {Object} element
       * @return {?}
       */
      idOrName : function(element) {
        return this.groups[element.name] || (this.checkable(element) ? element.name : element.id || element.name);
      },
      /**
       * @param {Object} element
       * @return {?}
       */
      validationTargetFor : function(element) {
        if (this.checkable(element)) {
          element = this.findByName(element.name).not(this.settings.ignore)[0];
        }
        return element;
      },
      /**
       * @param {Object} element
       * @return {?}
       */
      checkable : function(element) {
        return/radio|checkbox/i.test(element.type);
      },
      /**
       * @param {string} name
       * @return {?}
       */
      findByName : function(name) {
        return $(this.currentForm).find("[name='" + name + "']");
      },
      /**
       * @param {Array} value
       * @param {Object} element
       * @return {?}
       */
      getLength : function(value, element) {
        switch(element.nodeName.toLowerCase()) {
          case "select":
            return $("option:selected", element).length;
          case "input":
            if (this.checkable(element)) {
              return this.findByName(element.name).filter(":checked").length;
            }
          ;
        }
        return value.length;
      },
      /**
       * @param {?} param
       * @param {Object} element
       * @return {?}
       */
      depend : function(param, element) {
        return this.dependTypes[typeof param] ? this.dependTypes[typeof param](param, element) : true;
      },
      dependTypes : {
        /**
         * @param {?} param
         * @param {?} name
         * @return {?}
         */
        "boolean" : function(param, name) {
          return param;
        },
        /**
         * @param {?} param
         * @param {Object} element
         * @return {?}
         */
        "string" : function(param, element) {
          return!!$(param, element.form).length;
        },
        /**
         * @param {?} param
         * @param {?} element
         * @return {?}
         */
        "function" : function(param, element) {
          return param(element);
        }
      },
      /**
       * @param {Object} allow
       * @return {?}
       */
      optional : function(allow) {
        var val = this.elementValue(allow);
        return!$.validator.methods.required.call(this, val, allow) && "dependency-mismatch";
      },
      dateValid: function(txtDate){
            var currVal = txtDate;
            if (currVal == '')
                return false;

            //Declare Regex 
            var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
            var dtArray = currVal.match(rxDatePattern); // is format OK?

            if (dtArray == null)
                return false;

            //Checks for mm/dd/yyyy format.
        //  dtMonth = dtArray[1];
        //  dtDay= dtArray[3];
        //  dtYear = dtArray[5];
            //Checks for dd/mm/yyyy format.
            dtDay = dtArray[1];
            dtMonth = dtArray[3];
            dtYear = dtArray[5];
            if (dtMonth < 1 || dtMonth > 12)
                return false;
            else if (dtDay < 1 || dtDay > 31)
                return false;
            else if ((dtMonth == 4 || dtMonth == 6 || dtMonth == 9 || dtMonth == 11) && dtDay == 31)
                return false;
            else if (dtMonth == 2)
            {
                var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
                if (dtDay > 29 || (dtDay == 29 && !isleap))
                    return false;
            }
            return true;
      },
      /**
       * @param {Function} element
       * @return {undefined}
       */
      startRequest : function(element) {
        if (!this.pending[element.name]) {
          this.pendingRequest++;
          /** @type {boolean} */
          this.pending[element.name] = true;
        }
      },
      /**
       * @param {Function} element
       * @param {boolean} valid
       * @return {undefined}
       */
      stopRequest : function(element, valid) {
        this.pendingRequest--;
        if (this.pendingRequest < 0) {
          /** @type {number} */
          this.pendingRequest = 0;
        }
        delete this.pending[element.name];
        if (valid && (this.pendingRequest === 0 && (this.formSubmitted && this.form()))) {
          $(this.currentForm).submit();
          /** @type {boolean} */
          this.formSubmitted = false;
        } else {
          if (!valid && (this.pendingRequest === 0 && this.formSubmitted)) {
            $(this.currentForm).triggerHandler("invalid-form", [this]);
            /** @type {boolean} */
            this.formSubmitted = false;
          }
        }
      },
      /**
       * @param {Object} element
       * @return {?}
       */
      previousValue : function(element) {
        return $.data(element, "previousValue") || $.data(element, "previousValue", {
          old : null,
          valid : true,
          message : this.defaultMessage(element, "remote")
        });
      }
    },
    classRuleSettings : {
      required : {
        required : true
      },
      email : {
        email : true
      },
      url : {
        url : true
      },
      date : {
        date : true
      },
      dateISO : {
        dateISO : true
      },
      number : {
        number : true
      },
      digits : {
        digits : true
      },
      creditcard : {
        creditcard : true
      }
    },
    /**
     * @param {?} className
     * @param {?} rules
     * @return {undefined}
     */
    addClassRules : function(className, rules) {
      if (className.constructor === String) {
        this.classRuleSettings[className] = rules;
      } else {
        $.extend(this.classRuleSettings, className);
      }
    },
    /**
     * @param {?} element
     * @return {?}
     */
    classRules : function(element) {
      var deep = {};
      var classes = $(element).attr("class");
      if (classes) {
        $.each(classes.split(" "), function() {
          if (this in $.validator.classRuleSettings) {
            $.extend(deep, $.validator.classRuleSettings[this]);
          }
        });
      }
      return deep;
    },
    /**
     * @param {?} element
     * @return {?}
     */
    attributeRules : function(element) {
      var rules = {};
      var $element = $(element);
      var method;
      for (method in $.validator.methods) {
        var value;
        if (method === "required") {
          value = $element.get(0).getAttribute(method);
          if (value === "") {
            /** @type {boolean} */
            value = true;
          }
          /** @type {boolean} */
          value = !!value;
        } else {
          value = $element.attr(method);
        }
        if (value) {
          rules[method] = value;
        } else {
          if ($element[0].getAttribute("type") === method) {
            /** @type {boolean} */
            rules[method] = true;
          }
        }
      }
      if (rules.maxlength && /-1|2147483647|524288/.test(rules.maxlength)) {
        delete rules.maxlength;
      }
      return rules;
    },
    /**
     * @param {?} element
     * @return {?}
     */
    dataRules : function(element) {
      var method;
      var value;
      var rules = {};
      var $element = $(element);
      for (method in $.validator.methods) {
        value = $element.data("rule-" + method.toLowerCase());
        if (value !== undefined) {
          rules[method] = value;
        }
      }
      return rules;
    },
    /**
     * @param {Object} element
     * @return {?}
     */
    staticRules : function(element) {
      var rules = {};
      var validator = $.data(element.form, "validator");
      if (validator.settings.rules) {
        rules = $.validator.normalizeRule(validator.settings.rules[element.name]) || {};
      }
      return rules;
    },
    /**
     * @param {Object} rules
     * @param {Object} element
     * @return {?}
     */
    normalizeRules : function(rules, element) {
      $.each(rules, function(prop, val) {
        if (val === false) {
          delete rules[prop];
          return;
        }
        if (val.param || val.depends) {
          /** @type {boolean} */
          var keepRule = true;
          switch(typeof val.depends) {
            case "string":
              /** @type {boolean} */
              keepRule = !!$(val.depends, element.form).length;
              break;
            case "function":
              keepRule = val.depends.call(element, element);
              break;
          }
          if (keepRule) {
            rules[prop] = val.param !== undefined ? val.param : true;
          } else {
            delete rules[prop];
          }
        }
      });
      $.each(rules, function(rule, parameter) {
        rules[rule] = $.isFunction(parameter) ? parameter(element) : parameter;
      });
      $.each(["minlength", "maxlength"], function() {
        if (rules[this]) {
          /** @type {number} */
          rules[this] = Number(rules[this]);
        }
      });
      $.each(["rangelength"], function() {
        var regExpResultArray;
        if (rules[this]) {
          if ($.isArray(rules[this])) {
            /** @type {Array} */
            rules[this] = [Number(rules[this][0]), Number(rules[this][1])];
          } else {
            if (typeof rules[this] === "string") {
              regExpResultArray = rules[this].split(/[\s,]+/);
              /** @type {Array} */
              rules[this] = [Number(regExpResultArray[0]), Number(regExpResultArray[1])];
            }
          }
        }
      });
      if ($.validator.autoCreateRanges) {
        if (rules.min && rules.max) {
          /** @type {Array} */
          rules.range = [rules.min, rules.max];
          delete rules.min;
          delete rules.max;
        }
        if (rules.minlength && rules.maxlength) {
          /** @type {Array} */
          rules.rangelength = [rules.minlength, rules.maxlength];
          delete rules.minlength;
          delete rules.maxlength;
        }
      }
      return rules;
    },
    /**
     * @param {string} data
     * @return {?}
     */
    normalizeRule : function(data) {
      if (typeof data === "string") {
        var tmp = {};
        $.each(data.split(/\s/), function() {
          /** @type {boolean} */
          tmp[this] = true;
        });
        data = tmp;
      }
      return data;
    },
    /**
     * @param {string} name
     * @param {Function} method
     * @param {string} message
     * @return {undefined}
     */
    addMethod : function(name, method, message) {
      /** @type {Function} */
      $.validator.methods[name] = method;
      $.validator.messages[name] = message !== undefined ? message : $.validator.messages[name];
      if (method.length < 3) {
        $.validator.addClassRules(name, $.validator.normalizeRule(name));
      }
    },
    methods : {
      /**
       * @param {Array} value
       * @param {Object} element
       * @param {?} param
       * @return {?}
       */
      required : function(value, element, param) {
        if (!this.depend(param, element)) {
          return "dependency-mismatch";
        }
        if (element.nodeName.toLowerCase() === "select") {
          var codeSegments = $(element).val();
          return codeSegments && codeSegments.length > 0;
        }
        if (this.checkable(element)) {
          return this.getLength(value, element) > 0;
        }
        return $.trim(value).length > 0;
      },
      /**
       * @param {?} value
       * @param {Object} element
       * @param {string} param
       * @return {?}
       */
      remote : function(value, element, param) {
        if (this.optional(element)) {
          return "dependency-mismatch";
        }
        var previous = this.previousValue(element);
        if (!this.settings.messages[element.name]) {
          this.settings.messages[element.name] = {};
        }
        previous.originalMessage = this.settings.messages[element.name].remote;
        this.settings.messages[element.name].remote = previous.message;
        param = typeof param === "string" && {
          url : param
        } || param;
        if (previous.old === value) {
          return previous.valid;
        }
        previous.old = value;
        var validator = this;
        this.startRequest(element);
        var data = {};
        data[element.name] = value;
        $.ajax($.extend(true, {
          url : param,
          mode : "abort",
          port : "validate" + element.name,
          dataType : "json",
          data : data,
          /**
           * @param {string} response
           * @return {undefined}
           */
          success : function(response) {
            validator.settings.messages[element.name].remote = previous.originalMessage;
            /** @type {boolean} */
            var valid = response === true || response === "true";
            if (valid) {
              var submitted = validator.formSubmitted;
              validator.prepareElement(element);
              validator.formSubmitted = submitted;
              validator.successList.push(element);
              delete validator.invalid[element.name];
              validator.showErrors();
            } else {
              var errors = {};
              var message = response || validator.defaultMessage(element, "remote");
              errors[element.name] = previous.message = $.isFunction(message) ? message(value) : message;
              /** @type {boolean} */
              validator.invalid[element.name] = true;
              validator.showErrors(errors);
            }
            /** @type {boolean} */
            previous.valid = valid;
            validator.stopRequest(element, valid);
          }
        }, param));
        return "pending";
      },
      /**
       * @param {Array} value
       * @param {Object} element
       * @param {?} param
       * @return {?}
       */
      minlength : function(value, element, param) {
        var length = $.isArray(value) ? value.length : this.getLength($.trim(value), element);
        return this.optional(element) || length >= param;
      },
      /**
       * @param {Array} value
       * @param {Object} element
       * @param {?} param
       * @return {?}
       */
      maxlength : function(value, element, param) {
        var length = $.isArray(value) ? value.length : this.getLength($.trim(value), element);
        return this.optional(element) || length <= param;
      },
      /**
       * @param {Array} value
       * @param {Object} element
       * @param {Array} param
       * @return {?}
       */
      rangelength : function(value, element, param) {
        var length = $.isArray(value) ? value.length : this.getLength($.trim(value), element);
        return this.optional(element) || length >= param[0] && length <= param[1];
      },
      /**
       * @param {number} value
       * @param {Object} element
       * @param {number} param
       * @return {?}
       */
      min : function(value, element, param) {
        return this.optional(element) || value >= param;
      },
      /**
       * @param {number} value
       * @param {Object} element
       * @param {number} param
       * @return {?}
       */
      max : function(value, element, param) {
        return this.optional(element) || value <= param;
      },
      /**
       * @param {?} value
       * @param {Object} element
       * @param {Array} param
       * @return {?}
       */
      range : function(value, element, param) {
        return this.optional(element) || value >= param[0] && value <= param[1];
      },
      /**
       * @param {?} value
       * @param {Object} element
       * @return {?}
       */
      email : function(value, element) {
        return this.optional(element) || /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(value);
      },
      /**
       * @param {?} value
       * @param {Object} element
       * @return {?}
       */
      url : function(value, element) {
        return this.optional(element) || /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
      },
      /**
       * @param {?} value
       * @param {Object} element
       * @return {?}
       */
      date : function(value, element) {
//        return this.optional(element) || !/Invalid|NaN/.test((new Date(value)).toString());
            if(value === '__-__-____' || value === '__/__/____'){//para evitar error con la mascara
                return true;
            }
          return this.dateValid(value); 
      },
      /**
       * @param {?} value
       * @param {Object} element
       * @return {?}
       */
      dateISO : function(value, element) {
        return this.optional(element) || /^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/.test(value);
      },
      /**
       * @param {?} value
       * @param {Object} element
       * @return {?}
       */
      number : function(value, element) {
        return this.optional(element) || /^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(value);
      },
      /**
       * @param {?} value
       * @param {Object} element
       * @return {?}
       */
      digits : function(value, element) {
        return this.optional(element) || /^\d+$/.test(value);
      },
      /**
       * @param {string} value
       * @param {Object} element
       * @return {?}
       */
      creditcard : function(value, element) {
        if (this.optional(element)) {
          return "dependency-mismatch";
        }
        if (/[^0-9 \-]+/.test(value)) {
          return false;
        }
        /** @type {number} */
        var nCheck = 0;
        /** @type {number} */
        var nDigit = 0;
        /** @type {boolean} */
        var perm = false;
        value = value.replace(/\D/g, "");
        /** @type {number} */
        var n = value.length - 1;
        for (;n >= 0;n--) {
          var cDigit = value.charAt(n);
          /** @type {number} */
          nDigit = parseInt(cDigit, 10);
          if (perm) {
            if ((nDigit *= 2) > 9) {
              nDigit -= 9;
            }
          }
          nCheck += nDigit;
          /** @type {boolean} */
          perm = !perm;
        }
        return nCheck % 10 === 0;
      },
      /**
       * @param {?} value
       * @param {?} element
       * @param {?} param
       * @return {?}
       */
      equalTo : function(value, element, param) {
        var target = $(param);
        if (this.settings.onfocusout) {
          target.unbind(".validate-equalTo").bind("blur.validate-equalTo", function() {
            $(element).valid();
          });
        }
        return value === target.val();
      },
      regular: function(value, element){
            return this.optional(element) || /^[A-Za-záéíóúÁÉÍÓÚñÑ\d=#()¿?@_ -]+$/.test(value);
      },
      regular2: function(value, element){
            return this.optional(element) || /^[A-Za-záéíóúÁÉÍÓÚñÑ\d=#()¿?@_.,;: -]+$/.test(value);
      },
      decimal: function(value, element){
        if (value % 1 === 0) {
            return false;
        }
        else{
            return true;
        }
      },
      integer: function(value, element){
        if (value % 1 === 0) {
            return true;
        }
        else{
            return false;
        }
      }
    }
  });
  /** @type {function (string, (Array|string)): ?} */
  $.format = $.validator.format;
})(jQuery);
(function($) {
  var pendingRequests = {};
  if ($.ajaxPrefilter) {
    $.ajaxPrefilter(function(settings, dataAndEvents, xhr) {
      var port = settings.port;
      if (settings.mode === "abort") {
        if (pendingRequests[port]) {
          pendingRequests[port].abort();
        }
        pendingRequests[port] = xhr;
      }
    });
  } else {
    var ajax = $.ajax;
    /**
     * @param {Object} settings
     * @return {?}
     */
    $.ajax = function(settings) {
      var mode = ("mode" in settings ? settings : $.ajaxSettings).mode;
      var port = ("port" in settings ? settings : $.ajaxSettings).port;
      if (mode === "abort") {
        if (pendingRequests[port]) {
          pendingRequests[port].abort();
        }
        return pendingRequests[port] = ajax.apply(this, arguments);
      }
      return ajax.apply(this, arguments);
    };
  }
})(jQuery);
(function($) {
  $.extend($.fn, {
    /**
     * @param {string} delegate
     * @param {string} type
     * @param {Function} handler
     * @return {?} 
     */
    validateDelegate : function(delegate, type, handler) {
        this.unbind(type); /*AGREGADO POR RDCC */
      return this.bind(type, function(ev) {
        var target = $(ev.target);
        if (target.is(delegate)) {
          return handler.apply(target, arguments);
        }
      });
    }
  }); 
})(jQuery);