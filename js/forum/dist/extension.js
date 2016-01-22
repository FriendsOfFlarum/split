System.register('flagrow/split/addSplitControl', ['flarum/extend', 'flarum/app', 'flarum/utils/PostControls', 'flarum/components/Button', 'flarum/components/CommentPost', 'flarum/components/DiscussionPage', 'flagrow/split/components/SplitPostModal'], function (_export) {
    'use strict';

    var extend, app, PostControls, Button, CommentPost, DiscussionPage, SplitPostModal;
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
        }],
        execute: function () {
            _export('default', function () {

                extend(PostControls, 'moderationControls', function (items, post) {
                    var discussion = post.discussion();

                    if (post.isHidden() || post.contentType() !== 'comment' || !discussion.canSplit()) return;

                    items.add('splitFrom', [m(Button, {
                        icon: 'code-fork',
                        onclick: function onclick() {
                            return discussion.splitting(true);
                        },
                        className: 'flagrow-split-startSplitButton'
                    }, app.translator.trans('flagrow-split.forum.post_controls.split_button'))]);
                });

                extend(CommentPost.prototype, 'footerItems', function (items) {
                    var post = this.props.post;
                    var discussion = post.discussion();

                    if (post.isHidden() || post.contentType() !== 'comment' || !discussion.canSplit()) return;

                    items.add('splitTo', [m(Button, {
                        icon: 'code-fork',
                        className: 'flagrow-split-endSplitButton Button Button--link',
                        onclick: function onclick() {
                            return discussion.splitting(false);
                        },
                        // @todo the above is a temporary test solution, we need to implement the modal
                        //onclick: () => app.modal.show(new SplitPostModal(post)),
                        style: { display: discussion.splitting() ? "block" : "none" }
                    }, app.translator.trans('flagrow-split.forum.post_footer.split_button'))]);
                });
            });
        }
    };
});;
System.register('flagrow/split/components/SplitPostModal', ['flarum/components/Modal', 'flarum/components/Button'], function (_export) {
    'use strict';

    var Modal, Button, SplitPostModal;
    return {
        setters: [function (_flarumComponentsModal) {
            Modal = _flarumComponentsModal['default'];
        }, function (_flarumComponentsButton) {
            Button = _flarumComponentsButton['default'];
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
                    key: 'className',
                    value: function className() {
                        return 'SplitPostModal Modal--small';
                    }
                }, {
                    key: 'title',
                    value: function title() {
                        return app.translator.trans('flagrow-split.forum.split_post.title');
                    }
                }, {
                    key: 'content',
                    value: function content() {
                        if (this.success && !this.gotError) {
                            return [m('div', { className: 'Modal-body' }, [m('div', { className: 'Form Form--centered' }, [m('p', { className: 'helpText' }, app.translator.trans('flagrow-split.forum.split_post.confirmation_message')), m('div', { className: 'Form-group' }, [m(Button, {
                                className: 'Button Button--primary Button--block',
                                onclick: this.hide.bind(this)
                            }, app.translator.trans('flagrow-split.forum.split_post.dismiss_button'))])])])];
                        }

                        return [m('div', { className: 'Modal-body' }, [m('div', { className: 'Form Form--centered' }, [m('div', { className: 'Form-group' }, [m('label', {}, app.translator.trans('flagrow-split.forum.split_post.new_discussion_label')), m('input', {
                            name: 'new_discussion_title',
                            value: this.newDiscussionTitle(),
                            oninput: m.withAttr('value', this.newDiscussionTitle)
                        })]), m('div', { className: 'Form-group' }, [m(Button, {
                            className: 'Button Button--primary Button--block',
                            type: 'submit',
                            loading: this.loading,
                            disabled: !this.newDiscussionTitle()
                        }, app.translator.trans('flagrow-split.forum.split_post.submit_button'))])])])];
                    }
                }, {
                    key: 'onsubmit',
                    value: function onsubmit(e) {
                        var _this = this;

                        e.preventDefault();

                        this.loading = true;

                        var data = new FormData();
                        data.append('new_discussion_title', this.newDiscussionTitle());
                        data.append('actor', app.session.user);
                        data.append('post', this.props.post);

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

                        // app.store.createRecord('flags').save({
                        //     reason: this.reason() === 'other' ? null : this.reason(),
                        //     reasonDetail: this.reasonDetail(),
                        //     relationships: {
                        //         user: app.session.user,
                        //         post: this.props.post
                        //     }
                        // })
                        // .then(() => this.success = true)
                        // .finally(this.loaded.bind(this));
                    }
                }]);
                return SplitPostModal;
            })(Modal);

            _export('default', SplitPostModal);
        }
    };
});;
System.register('flagrow/split/main', ['flarum/extend', 'flarum/Model', 'flagrow/split/addSplitControl', 'flagrow/split/extendDiscussionPage'], function (_export) {
    'use strict';

    var extend, Model, addSplitControl, extendDiscussionPage;
    return {
        setters: [function (_flarumExtend) {
            extend = _flarumExtend.extend;
        }, function (_flarumModel) {
            Model = _flarumModel['default'];
        }, function (_flagrowSplitAddSplitControl) {
            addSplitControl = _flagrowSplitAddSplitControl['default'];
        }, function (_flagrowSplitExtendDiscussionPage) {
            extendDiscussionPage = _flagrowSplitExtendDiscussionPage['default'];
        }],
        execute: function () {

            app.initializers.add('flagrow-split', function (app) {

                app.store.models.discussions.prototype.canSplit = Model.attribute('canSplit');

                extendDiscussionPage();

                //addSplitControl();
            });
        }
    };
});;
System.register('flagrow/split/utils/SplitController', [], function (_export) {
    'use strict';

    var SplitController;
    return {
        setters: [],
        execute: function () {
            SplitController = (function () {
                function SplitController() {
                    babelHelpers.classCallCheck(this, SplitController);
                }

                babelHelpers.createClass(SplitController, [{
                    key: 'init',
                    value: function init() {
                        console.log('SplitController init');
                        this.splitting = false;
                    }
                }, {
                    key: 'isSplitting',
                    value: function isSplitting() {
                        console.log('checked splitting, result:' + this.splitting);
                        return this.splitting;
                    }
                }, {
                    key: 'startSplitting',
                    value: function startSplitting() {
                        console.log('Started splitting');
                        this.splitting = true;
                        m.redraw.strategy("diff");
                        m.redraw();
                    }
                }, {
                    key: 'endSplitting',
                    value: function endSplitting() {
                        console.log('Ended splitting');
                        this.splitting = false;
                    }
                }]);
                return SplitController;
            })();

            _export('default', SplitController);
        }
    };
});;
System.register('flagrow/split/extendDiscussionPage', ['flarum/extend', 'flarum/components/DiscussionPage', 'flarum/utils/SplitController'], function (_export) {
    'use strict';

    var extend, DiscussionPage, Splitter;
    return {
        setters: [function (_flarumExtend) {
            extend = _flarumExtend.extend;
        }, function (_flarumComponentsDiscussionPage) {
            DiscussionPage = _flarumComponentsDiscussionPage['default'];
        }, function (_flarumUtilsSplitController) {
            Splitter = _flarumUtilsSplitController['default'];
        }],
        execute: function () {
            _export('default', function () {
                extend(DiscussionPage, 'init', function () {
                    this.splitter = new Splitter();
                });
            });
        }
    };
});