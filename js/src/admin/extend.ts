import Extend from 'flarum/common/extenders';

export default [
  new Extend.Admin().permission(
    () => ({
      icon: 'fas fa-code-branch',
      label: app.translator.trans('fof-split.admin.permissions.split_discussion_label'),
      permission: 'discussion.split',
    }),
    'moderate'
  ),
];
