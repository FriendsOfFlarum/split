import Form from 'flarum/common/components/Form';
import app from 'flarum/forum/app';
import FormModal, { IFormModalAttrs } from 'flarum/common/components/FormModal';
import Button from 'flarum/common/components/Button';
import Stream from 'flarum/common/utils/Stream';
import type Post from 'flarum/common/models/Post';
import type Mithril from 'mithril';

export interface SplitPostModalAttrs extends IFormModalAttrs {
  post: Post;
}

export default class SplitPostModal extends FormModal<SplitPostModalAttrs> {
  newDiscussionTitle!: Stream<string>;

  oninit(vnode: Mithril.Vnode<SplitPostModalAttrs, this>) {
    super.oninit(vnode);

    this.newDiscussionTitle = Stream('');
  }

  className(): string {
    return 'SplitPostModal Modal--small';
  }

  title(): Mithril.Children {
    return app.translator.trans('fof-split.forum.modal.title');
  }

  content(): Mithril.Children {
    return [
      <div className="Modal-body">
        <Form className="Form--centered">
          <div className="Form-group">
            <label for="new_discussion_title">{app.translator.trans('fof-split.forum.modal.new_discussion_label')}</label>
            <input className="FormControl" name="new_discussion_title" bidi={this.newDiscussionTitle} />
          </div>
          <div className="Form-group">
            <Button className="Button Button--primary Button--block" type="submit" loading={this.loading} disabled={!this.newDiscussionTitle()}>
              {app.translator.trans('fof-split.forum.modal.submit_button')}
            </Button>
          </div>
        </Form>
      </div>,
    ];
  }

  onsubmit(e: Event): void {
    e.preventDefault();

    this.loading = true;

    const data = new FormData();

    data.append('title', this.newDiscussionTitle());
    data.append('start_post_id', app.__fof_split.splitController?.startPostId!);
    data.append('end_post_number', this.attrs.post.number()!.toString());

    app
      .request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/split',
        serialize: (raw: any) => raw,
        body: data,
      })
      .then((data: any) => {
        let discussion: any = {};

        discussion.id = Stream(data.data.id);
        discussion.slug = Stream(data.data.attributes.slug);
        discussion.startUser = Stream(data.data.attributes.startUser);
        discussion.isUnread = Stream(data.data.attributes.isUnread);

        app.__fof_split.splitController?.reset();

        this.hide();
        m.route.set(app.route.discussion(discussion));
      }, this.loaded.bind(this));
  }
}
