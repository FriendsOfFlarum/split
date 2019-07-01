import { extend } from 'flarum/extend';
import app from 'flarum/app';
import PermissionGrid from 'flarum/components/PermissionGrid';

app.initializers.add('flagrow-split', app => {
    extend(PermissionGrid.prototype, 'moderateItems', items => {
    items.add('split', {
      icon: 'fas fa-code-branch',
      label: app.translator.trans('fof-split.admin.permissions.split_discussion_label'),
      permission: 'discussion.split'
    }, 65);
  });
});
