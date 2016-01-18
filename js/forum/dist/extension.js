System.register('flagrow/split/addSplitControl', ['flarum/extend', 'flarum/app', 'flarum/utils/PostControls', 'flarum/components/Button'], function (_export) {

    //import SplitPostModal from 'flagrow/split/components/SplitPostModal';

    'use strict';

    var extend, app, PostControls, Button;
    return {
        setters: [function (_flarumExtend) {
            extend = _flarumExtend.extend;
        }, function (_flarumApp) {
            app = _flarumApp['default'];
        }, function (_flarumUtilsPostControls) {
            PostControls = _flarumUtilsPostControls['default'];
        }, function (_flarumComponentsButton) {
            Button = _flarumComponentsButton['default'];
        }],
        execute: function () {
            _export('default', function () {
                extend(PostControls, 'userControls', function (items, post) {
                    if (post.isHidden() || post.contentType() !== 'comment' || !post.canSplit()) return;

                    items.add('split', [m(Button, {
                        icon: 'code-fork'
                        //onclick: () => app.modal.show(new SplitPostModal({post}))
                    }, app.translator.trans('flagrow-split.forum.post_controls.split_button'))]);
                });
            });
        }
    };
});;
System.register('flagrow/split/main', ['flarum/extend', 'flarum/Model', 'flagrow/split/addSplitControl'], function (_export) {
    'use strict';

    var extend, Model, addSplitControl;
    return {
        setters: [function (_flarumExtend) {
            extend = _flarumExtend.extend;
        }, function (_flarumModel) {
            Model = _flarumModel['default'];
        }, function (_flagrowSplitAddSplitControl) {
            addSplitControl = _flagrowSplitAddSplitControl['default'];
        }],
        execute: function () {

            app.initializers.add('flagrow-split', function (app) {
                app.store.models.posts.prototype.canSplit = Model.attribute('canSplit');

                addSplitControl();
            });
        }
    };
});