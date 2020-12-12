import app from 'flarum/app';

app.initializers.add('fof-split', (app) => {
    app.extensionData.for('fof-split')
        .registerPermission({
            icon: 'fas fa-code-branch',
            label: app.translator.trans('fof-split.admin.permissions.split_discussion_label'),
            permission: 'discussion.split',
        }, 'moderate');
});
