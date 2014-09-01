/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (typeof Mage == 'undefined') {
    Mage = {};
}
if (typeof Mage.GoogleShopping == 'undefined') {
    Mage.GoogleShopping = {
        productForm: null,
        productGrid: null,

        poller: {
            timeout: 10000,
            interval: null,

            start: function(url) {
                this.interval = setInterval(this.request.bind(this, url), this.timeout)
            },

            stop: function() {
                clearInterval(this.interval);
            },

            request: function(url) {
                Ajax.Responders.unregister(varienLoaderHandler.handler);
                new Ajax.Request(url, {
                    method: 'get',
                    onComplete: (function (response) {
                        this.onSuccess(response.responseJSON.is_running);
                    }).bind(this)
                })
            },

            onSuccess: function(isFinished) {

            }
        },

        startAction: function (form) {
            Ajax.Responders.unregister(varienLoaderHandler.handler);
            this.lock();
            new Ajax.Request(form.action, {
                'method': 'post',
                'parameters': form.serialize(true),
                'onSuccess': Mage.GoogleShopping.onSuccess.bind(Mage.GoogleShopping, this),
                'onFailure': Mage.GoogleShopping.onFailure.bind(Mage.GoogleShopping, this)
            });
        },

        onSuccess: function(form, response) {
            if (response.responseJSON && typeof response.responseJSON.redirect != 'undefined') {
                setLocation(response.responseJSON.redirect);
            } else {
                window.location.reload();
            }
        },

        onFailure: function() {
            window.location.reload();
        },

        lock: function() {
            if (this.itemForm) {
                this.lockButton($(this.itemForm).down('button'));
            }
            if (this.productForm) {
                this.lockButton($(this.productForm).down('button'));
            }
            this.addMessage();
        },

        addMessage: function() {
            var messageBox = $('messages');
            var messageList = $(messageBox).down('ul.messages');
            if (!messageList) {
                messageList = new Element('ul', {class: 'messages'});
                messageBox.update(messageList);
            }
            var message = '<li class="notice-msg">' + this.runningMessage + '</li>';
            messageList.update(message);
        },

        lockButton: function (button) {
            $(button).addClassName('disabled loading');
            $(button).disabled = true;
        }
    }
}


Event.observe(document, 'dom:loaded', function() {
    Mage.GoogleShopping.itemForm = items_massactionJsObject.form;
    items_massactionJsObject.prepareForm = items_massactionJsObject.prepareForm.wrap(function (proceed) {
        Mage.GoogleShopping.itemForm = proceed();
        Mage.GoogleShopping.itemForm.submit = function(){ Mage.GoogleShopping.startAction(this); };
        return Mage.GoogleShopping.itemForm;
    });

    Mage.GoogleShopping.productForm = googleshopping_selection_search_grid__massactionJsObject.form;
    googleshopping_selection_search_grid__massactionJsObject.prepareForm = googleshopping_selection_search_grid__massactionJsObject.prepareForm.wrap(function (proceed) {
        Mage.GoogleShopping.productForm = proceed();
        Mage.GoogleShopping.productForm.submit = function() { Mage.GoogleShopping.startAction(this) };
        return Mage.GoogleShopping.productForm;
    });

    Mage.GoogleShopping.itemForm.submit = function(){ Mage.GoogleShopping.startAction(this); };
    Mage.GoogleShopping.productForm.submit = function() { Mage.GoogleShopping.startAction(this) };
    if (Mage.GoogleShopping.isProcessRunning) {
        Mage.GoogleShopping.lock();
        Mage.GoogleShopping.poller.onSuccess = function(isRunning){
            if (!isRunning) {
                this.stop()
                Mage.GoogleShopping.onSuccess();
            }
        }
        Mage.GoogleShopping.poller.start(Mage.GoogleShopping.statusUrl);
    }
});
