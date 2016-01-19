import Modal from 'flarum/components/Modal';
import Button from 'flarum/components/Button';

export default class SplitPostModal extends Modal {
    init() {
        super.init();

        this.success = false;

        this.gotError = false;

        this.newDiscussionTitle = m.prop('');
    }

    className() {
        return 'SplitPostModal Modal--small';
    }

    title() {
        return app.translator.trans('flagrow-split.forum.split_post.title');
    }

    content() {
        if (this.success && ! this.gotError) {
            return [
                m('div', {className: 'Modal-body'}, [
                    m('div', {className: 'Form Form--centered'}, [
                        m('p', {className: 'helpText'}, app.translator.trans('flagrow-split.forum.split_post.confirmation_message')),
                        m('div', {className: 'Form-group'}, [
                            m(Button, {
                                className: 'Button Button--primary Button--block',
                                onclik: this.hide.bind(this)
                            }, app.translator.trans('flagrow-split.forum.split_post.dismiss_button'))
                        ])
                    ])
                ])
            ];
        }

        return [
            m('div', {className: 'Modal-body'}, [
                m('div', {className: 'Form Form--centered'}, [
                    m('div', {className: 'Form-group'}, [
                        m('label', {},  app.translator.trans('flagrow-split.forum.split_post.new_discussion_label')),
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
                        }, app.translator.trans('flagrow-split.forum.split_post.submit_button'))
                    ])
                ])
            ])
        ];
    }

    onsubmit(e) {
        e.preventDefault();

        this.loading = true;

        const data = new FormData();
        data.append('new_discussion_title', this.newDiscussionTitle());
        data.append('actor', app.session.user);
        data.append('post', this.props.post);    

        app.request({
            method: 'POST',
            url: app.forum.attribute('apiUrl') + '/split',
            serialize: raw => raw,
            data
        })
        .then(() => this.success = true)
        .finally(this.loaded.bind(this));

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
}
