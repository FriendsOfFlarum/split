import Modal from 'flarum/components/Modal';
import Button from 'flarum/components/Button';
import Discussion from 'flarum/models/Discussion';

import SplitController from 'flagrow/split/components/SplitController';

export default class SplitPostModal extends Modal {
    init() {
        super.init();

        this.newDiscussionTitle = m.prop('');

    }

    setController(splitController) {
        this.splitController = splitController;
    }

    className() {
        return 'SplitPostModal Modal--small';
    }

    title() {
        return app.translator.trans('flagrow-split.forum.modal.title');
    }

    content() {
        return [
            m('div', {className: 'Modal-body'}, [
                m('div', {className: 'Form Form--centered'}, [
                    m('div', {className: 'Form-group'}, [
                        m('label', {},  app.translator.trans('flagrow-split.forum.modal.new_discussion_label')),
                        m('input', {
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
                        }, app.translator.trans('flagrow-split.forum.modal.submit_button'))
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
        data.append('start_post_id', this.splitController.startPost());
        data.append('end_post_id', this.splitController.endPost());

        app.request({
            method: 'POST',
            url: app.forum.attribute('apiUrl') + '/split',
            serialize: raw => raw,
            data
        }).then(
            discussion => {
                app.cache.discussionList.addDiscussion(discussion);
                this.success = true;
                //this.hide();
                m.route(app.route.discussion(new discussion));
            },
            this.loaded.bind(this)
        );
    }
}
