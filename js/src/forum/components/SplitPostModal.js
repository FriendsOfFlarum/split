import Modal from "flarum/components/Modal";
import Button from "flarum/components/Button";

export default class SplitPostModal extends Modal {
    init() {
        super.init();

        this.newDiscussionTitle = m.prop('');

    }

    setController(controller) {
        this.split = controller;
    }

    className() {
        return 'SplitPostModal Modal--small';
    }

    title() {
        return app.translator.trans('fof-split.forum.modal.title');
    }

    content() {
        return [
            m('div', {className: 'Modal-body'}, [
                m('div', {className: 'Form Form--centered'}, [
                    m('div', {className: 'Form-group'}, [
                        m('label', {},  app.translator.trans('fof-split.forum.modal.new_discussion_label')),
                        m('input', {
                            className: 'FormControl',
                            name: 'new_discussion_title',
                            value: this.newDiscussionTitle(),
                            oninput: m.withAttr('value', this.newDiscussionTitle)
                        })
                    ]),
                    m('div', {className: 'Form-group'}, [
                        m(Button, {
                            className: 'Button Button--primary Button--block',
                            type: 'submit',
                            loading: this.loading,
                            disabled: !this.newDiscussionTitle()
                        }, app.translator.trans('fof-split.forum.modal.submit_button'))
                    ])
                ])
            ])
        ];
    }

    onsubmit(e) {
        e.preventDefault();

        this.loading = true;

        const data = new FormData();

        data.append('title', this.newDiscussionTitle());
        data.append('start_post_id', this.split.startPostId);
        data.append('end_post_number', this.split.endPostNumber);

        app.request({
            method: 'POST',
            url: app.forum.attribute('apiUrl') + '/split',
            serialize: raw => raw,
            data
        }).then(
            data => {
                let discussion = {};
                discussion.id = m.prop(data.data.id);
                discussion.slug = m.prop(data.data.attributes.slug);
                discussion.startUser = m.prop(data.data.attributes.startUser);
                discussion.isUnread = m.prop(data.data.attributes.isUnread);
                this.hide();
                m.route(app.route.discussion(discussion));
            },
            this.loaded.bind(this)
        );
    }
}
