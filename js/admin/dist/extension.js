System.register('flagrow/split/main', ['flarum/extend', 'flarum/app', 'flarum/components/PermissionGrid'], function (_export) {

  //import addImageUploadPane from 'flagrow/image-upload/addImageUploadPane'

  'use strict';

  var extend, app, PermissionGrid;
  return {
    setters: [function (_flarumExtend) {
      extend = _flarumExtend.extend;
    }, function (_flarumApp) {
      app = _flarumApp['default'];
    }, function (_flarumComponentsPermissionGrid) {
      PermissionGrid = _flarumComponentsPermissionGrid['default'];
    }],
    execute: function () {
      app.initializers.add('flagrow-split', function (app) {
        //addSplitPane();

        extend(PermissionGrid.prototype, 'moderateItems', function (items) {
          items.add('splitDiscussion', {
            icon: 'code-fork',
            label: app.translator.trans('flagrow-split.admin.permissions.split_discussion_label'),
            permission: 'discussion.split'
          }, 65);
        });
      });
    }
  };
});