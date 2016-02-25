System.register('flagrow/split/addSplitControl', ['flarum/extend', 'flarum/app', 'flarum/utils/PostControls', 'flarum/components/Button', 'flarum/components/CommentPost', 'flarum/components/DiscussionPage', 'flagrow/split/components/SplitPostModal', 'flagrow/split/components/SplitController'], function (_export) {
    'use strict';

    var extend, app, PostControls, Button, CommentPost, DiscussionPage, SplitPostModal, SplitController;
    return {
        setters: [function (_flarumExtend) {
            extend = _flarumExtend.extend;
        }, function (_flarumApp) {
            app = _flarumApp['default'];
        }, function (_flarumUtilsPostControls) {
            PostControls = _flarumUtilsPostControls['default'];
        }, function (_flarumComponentsButton) {
            Button = _flarumComponentsButton['default'];
        }, function (_flarumComponentsCommentPost) {
            CommentPost = _flarumComponentsCommentPost['default'];
        }, function (_flarumComponentsDiscussionPage) {
            DiscussionPage = _flarumComponentsDiscussionPage['default'];
        }, function (_flagrowSplitComponentsSplitPostModal) {
            SplitPostModal = _flagrowSplitComponentsSplitPostModal['default'];
        }, function (_flagrowSplitComponentsSplitController) {
            SplitController = _flagrowSplitComponentsSplitController['default'];
        }],
        execute: function () {
            _export('default', function (splitController) {

                extend(PostControls, 'moderationControls', function (items, post) {
                    var discussion = post.discussion();

                    if (post.contentType() !== 'comment' || !discussion.canSplit() || post.data.attributes.number == 1) return;

                    items.add('splitFrom', [m(Button, {
                        icon: 'code-fork',
                        className: 'flagrow-split-startSplitButton',
                        // i'm not sure whether setting this attribute on app.current is the correct way,
                        // there is a discussion property on this object though
                        // luceos on feb 7 2016
                        onclick: function onclick() {
                            splitController.start(post.data.attributes.number, discussion.data.id);
                            splitController.log();
                        }
                    }, app.translator.trans('flagrow-split.forum.post_controls.split_button'))]);
                });

                extend(CommentPost.prototype, 'footerItems', function (items) {
                    var post = this.props.post;
                    var discussion = post.discussion();

                    if (post.contentType() !== 'comment' || !discussion.canSplit() || post.data.attributes.number == 1) return;

                    items.add('splitTo', [m(Button, {
                        icon: 'code-fork',
                        className: 'flagrow-split-endSplitButton Button Button--link',
                        //onclick: () => {app.current.splitting = false},
                        // @todo the above is a temporary test solution, we need to implement the modal
                        onclick: function onclick() {
                            splitController.end(post.data.attributes.number);
                            splitController.log();
                            var splitModal = new SplitPostModal();
                            splitModal.setController(splitController);
                            app.modal.show(splitModal);
                        },
                        style: { display: 'none' }
                    }, app.translator.trans('flagrow-split.forum.post_footer.split_button'))]);
                });
            });
        }
    };
});;
System.register('flagrow/split/components/SplitController', [], function (_export) {
    'use strict';

    var SplitController;
    return {
        setters: [],
        execute: function () {
            SplitController = (function () {
                function SplitController() {
                    babelHelpers.classCallCheck(this, SplitController);

                    this._isSplitting = false;
                }

                babelHelpers.createClass(SplitController, [{
                    key: 'start',
                    value: function start(postId, discussionId) {
                        // should not be necessary
                        if (postId == 1) return;

                        this._startPost = postId;
                        this._discussion = discussionId;
                        this._isSplitting = true;

                        $('.PostStream-item').each(function () {
                            var postIndex = $(this).attr('data-number');
                            if (postIndex > postId) {
                                $('.flagrow-split-endSplitButton', $(this)).show();
                            }
                        });
                        $('.flagrow-split-startSplitButton').hide();
                    }
                }, {
                    key: 'end',
                    value: function end(postId) {
                        this._endPost = postId;
                    }
                }, {
                    key: 'startPost',
                    value: function startPost() {
                        return this._startPost;
                    }
                }, {
                    key: 'endPost',
                    value: function endPost() {
                        return this._endPost;
                    }
                }, {
                    key: 'reset',
                    value: function reset() {
                        this._isSplitting = false;
                        this._startPost = null;
                        this._endPost = null;
                    }
                }, {
                    key: 'log',
                    value: function log() {
                        console.log('splitting:' + this._isSplitting);
                        console.log('discussion:' + this._discussion);
                        console.log('startPost:' + this._startPost);
                        console.log('endPost:' + this._endPost);
                    }
                }]);
                return SplitController;
            })();

            _export('default', SplitController);
        }
    };
});;
System.register('flagrow/split/components/SplitPostModal', ['flarum/components/Modal', 'flarum/components/Button', 'flagrow/split/components/SplitController'], function (_export) {
    'use strict';

    var Modal, Button, SplitController, SplitPostModal;
    return {
        setters: [function (_flarumComponentsModal) {
            Modal = _flarumComponentsModal['default'];
        }, function (_flarumComponentsButton) {
            Button = _flarumComponentsButton['default'];
        }, function (_flagrowSplitComponentsSplitController) {
            SplitController = _flagrowSplitComponentsSplitController['default'];
        }],
        execute: function () {
            SplitPostModal = (function (_Modal) {
                babelHelpers.inherits(SplitPostModal, _Modal);

                function SplitPostModal() {
                    babelHelpers.classCallCheck(this, SplitPostModal);
                    babelHelpers.get(Object.getPrototypeOf(SplitPostModal.prototype), 'constructor', this).apply(this, arguments);
                }

                babelHelpers.createClass(SplitPostModal, [{
                    key: 'init',
                    value: function init() {
                        babelHelpers.get(Object.getPrototypeOf(SplitPostModal.prototype), 'init', this).call(this);

                        this.success = false;

                        this.gotError = false;

                        this.newDiscussionTitle = m.prop('');
                    }
                }, {
                    key: 'setController',
                    value: function setController(splitController) {
                        this.splitController = splitController;

                        this.splitController.log();
                    }
                }, {
                    key: 'className',
                    value: function className() {
                        return 'SplitPostModal Modal--small';
                    }
                }, {
                    key: 'title',
                    value: function title() {
                        return app.translator.trans('flagrow-split.forum.modal.title');
                    }
                }, {
                    key: 'content',
                    value: function content() {
                        if (this.success && !this.gotError) {
                            return [m('div', { className: 'Modal-body' }, [m('div', { className: 'Form Form--centered' }, [m('p', { className: 'helpText' }, app.translator.trans('flagrow-split.forum.modalconfirmation_message')), m('div', { className: 'Form-group' }, [m(Button, {
                                className: 'Button Button--primary Button--block',
                                onclick: this.hide.bind(this)
                            }, app.translator.trans('flagrow-split.forum.modal.dismiss_button'))])])])];
                        }

                        return [m('div', { className: 'Modal-body' }, [m('div', { className: 'Form Form--centered' }, [m('div', { className: 'Form-group' }, [m('label', {}, app.translator.trans('flagrow-split.forum.modal.new_discussion_label')), m('input', {
                            name: 'new_discussion_title',
                            value: this.newDiscussionTitle(),
                            oninput: m.withAttr('value', this.newDiscussionTitle)
                        })]), m('div', { className: 'Form-group' }, [m(Button, {
                            className: 'Button Button--primary Button--block',
                            type: 'submit',
                            loading: this.loading,
                            disabled: !this.newDiscussionTitle()
                        }, app.translator.trans('flagrow-split.forum.modal.submit_button'))])])])];
                    }
                }, {
                    key: 'onsubmit',
                    value: function onsubmit(e) {
                        var _this = this;

                        e.preventDefault();

                        this.loading = true;

                        var data = new FormData();

                        data.append('title', this.newDiscussionTitle());
                        data.append('start_post_id', this.splitController.startPost());
                        data.append('end_post_id', this.splitController.endPost());

                        app.request({
                            method: 'POST',
                            url: app.forum.attribute('apiUrl') + '/split',
                            serialize: function serialize(raw) {
                                return raw;
                            },
                            data: data
                        }).then(function () {
                            return _this.success = true;
                        })['finally'](this.loaded.bind(this));
                    }
                }]);
                return SplitPostModal;
            })(Modal);

            _export('default', SplitPostModal);
        }
    };
});;
System.register('flagrow/split/extendDiscussionPage', ['flarum/extend', 'flarum/components/DiscussionPage'], function (_export) {
    'use strict';

    var extend, DiscussionPage;
    return {
        setters: [function (_flarumExtend) {
            extend = _flarumExtend.extend;
        }, function (_flarumComponentsDiscussionPage) {
            DiscussionPage = _flarumComponentsDiscussionPage['default'];
        }],
        execute: function () {
            _export('default', function () {
                extend(DiscussionPage.prototype, 'init', function () {
                    this.splitting = false;
                });
            });
        }
    };
});;
System.register('flagrow/split/main', ['flarum/extend', 'flarum/Model', 'flarum/models/Discussion', 'flagrow/split/addSplitControl', 'flagrow/split/components/SplitController'], function (_export) {

    //import extendDiscussionPage from 'flagrow/split/extendDiscussionPage';

    'use strict';

    var extend, Model, Discussion, addSplitControl, SplitController;
    return {
        setters: [function (_flarumExtend) {
            extend = _flarumExtend.extend;
        }, function (_flarumModel) {
            Model = _flarumModel['default'];
        }, function (_flarumModelsDiscussion) {
            Discussion = _flarumModelsDiscussion['default'];
        }, function (_flagrowSplitAddSplitControl) {
            addSplitControl = _flagrowSplitAddSplitControl['default'];
        }, function (_flagrowSplitComponentsSplitController) {
            SplitController = _flagrowSplitComponentsSplitController['default'];
        }],
        execute: function () {
            app.initializers.add('flagrow-split', function (app) {

                app.store.models.discussions.prototype.canSplit = Model.attribute('canSplit');

                //extendDiscussionPage();

                var splitController = new SplitController();
                console.log(splitController);

                addSplitControl(splitController);
            });
        }
    };
});