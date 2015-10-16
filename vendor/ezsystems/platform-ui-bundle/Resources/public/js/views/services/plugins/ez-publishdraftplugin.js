/*
 * Copyright (C) eZ Systems AS. All rights reserved.
 * For full copyright and license information view LICENSE file distributed with this source code.
 */
YUI.add('ez-publishdraftplugin', function (Y) {
    "use strict";
    /**
     * Provides the publish draft plugin
     *
     * @module ez-publishdraftplugin
     */
    Y.namespace('eZ.Plugin');

    /**
     * Publish draft plugin. It publishes the version hold by the host object (a
     * service) when the publishAction event is triggered.
     *
     * @namespace eZ.Plugin
     * @class PublishDraft
     * @constructor
     * @extends eZ.Plugin.ViewServiceBase
     */
    Y.eZ.Plugin.PublishDraft = Y.Base.create('publishDraftPlugin', Y.eZ.Plugin.ViewServiceBase, [], {
        initializer: function () {
            this.onHostEvent('*:publishAction', this._publishDraft);
        },

        /**
         * Event handler for the publishAction event. It publishes the version
         * if the form is valid and redirect the user to the URL hold by the
         * `publishRedirectionUrl` attribute.
         *
         * @method _publishDraft
         * @protected
         * @param {Object} e publishAction event facade
         */
        _publishDraft: function (e) {
            var service = this.get('host'),
                content = service.get('content'),
                app = service.get('app');

            if ( !e.formIsValid ) {
                return;
            }
            service.fire('notify', {
                notification: {
                    identifier: this._buildNotificationIdentifier(content.get('id')),
                    text: 'Publishing the content',
                    state: 'started',
                    timeout: 5,
                },
            });

            app.set('loading', true);
            if ( content.isNew() ) {
                this._createPublishContent(e.fields);
            } else {
                this._savePublishVersion(e.fields);
            }
        },

        /**
         * Redirects the user after the publishing process
         *
         * @method _publishDraftCallback
         * @protected
         */
        _publishDraftCallback: function (error) {
            var service = this.get('host'),
                app = this.get('host').get('app'),
                content = service.get('content');

            if ( error ) {
                this._notifyError(content.get('id'));
                app.set('loading', false);
                return;
            }

            content.load({api: service.get('capi')}, function (error, response) {
                /**
                 * Fired when the draft is published
                 *
                 * @event publishedDraft
                 * @param {eZ.Content} content
                 */
                service.fire('publishedDraft', {content: content});
                app.navigate(service.get('publishRedirectionUrl'));
            });
        },

        /**
         * Creates a draft of a new content with the given fields and directly
         * tries to publish it.
         *
         * @method _createPublishContent
         * @param Array fields the fields structures coming from the
         * publishAction event
         * @protected
         */
        _createPublishContent: function (fields) {
            var service = this.get('host'),
                capi = service.get('capi'),
                version = service.get('version'),
                content = service.get('content'),
                options = {api: capi};

            content.save({
                api: capi,
                languageCode: service.get('languageCode'),
                contentType: service.get('contentType'),
                parentLocation: service.get('parentLocation'),
                fields: fields,
            }, Y.bind(function (error, response) {
                if ( error ) {
                    this._notifyError(content.get('id'));
                    service.get('app').set('loading', false);
                    return;
                }
                version.setAttrs(version.parse({document: response.document.Content.CurrentVersion}));
                version.publishVersion(options, Y.bind(this._publishDraftCallback, this));
            }, this));
        },

        /**
         * Sets the given fields on the version and publishes it. This method is
         * called in the case where the content already exists in the repository
         * and the user wants to publish a new version of it.
         *
         * @method _savePublishVersion
         * @param Array fields the fields structures coming from the
         * publishAction event
         * @protected
         */
        _savePublishVersion: function (fields) {
            var service = this.get('host'),
                version = service.get('version'),
                content = service.get('content'),
                that = this;

            version.save({
                api: service.get('capi'),
                fields: fields,
                contentId: content.get('id'),
                languageCode: service.get('languageCode'),
                publish: true,
            }, Y.bind(that._publishDraftCallback, that));
        },

        /**
         * Notifies the editor about a publishing error
         *
         * @method _notifyError
         * @protected
         * @param {String} contentId
         */
        _notifyError: function (contentId) {
            this.get('host').fire('notify', {
                notification: {
                    identifier: this._buildNotificationIdentifier(contentId),
                    text: 'An error occured while publishing the draft',
                    state: 'error',
                    timeout: 0,
                },
            });
        },

        /**
         * Builds the notification identifier for the publish notification
         *
         * @method _buildNotificationIdentifier
         * @param {Boolean} isNew
         * @param {eZ.Content} content
         * @protected
         */
        _buildNotificationIdentifier: function (contentId) {
            return 'publish-' + contentId + '-' + this.get('host').get('languageCode');
        },
    }, {
        NS: 'publishDraft',
    });

    Y.eZ.PluginRegistry.registerPlugin(
        Y.eZ.Plugin.PublishDraft, ['contentEditViewService', 'contentCreateViewService']
    );
});
