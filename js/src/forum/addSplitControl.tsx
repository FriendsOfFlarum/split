import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import PostControls from 'flarum/forum/utils/PostControls';
import Button from 'flarum/common/components/Button';
import CommentPost from 'flarum/forum/components/CommentPost';
import type Post from 'flarum/common/models/Post';
import ItemList from 'flarum/common/utils/ItemList';
import Mithril from 'mithril';

export default function () {
  extend(PostControls, 'moderationControls', function (items: ItemList<Mithril.Children>, post: Post) {
    const discussion = post.discussion();

    if (post.contentType() !== 'comment' || !discussion.canSplit() || post.number() == 1) return;

    if (!app.__fof_split.splitting) {
      items.add('splitFrom', [
        m(
          Button,
          {
            icon: 'fas fa-code-branch',
            className: 'flagrow-split-startSplitButton',
            onclick: () => {
              app.__fof_split.splitController?.start(post.id()!, post.number()!);
            },
          },
          app.translator.trans('fof-split.forum.split.from')
        ),
      ]);
    }
  });

  extend(CommentPost.prototype, 'oninit', function () {
    this.subtree.check(() => app.__fof_split.splitting);
  });

  extend(CommentPost.prototype, 'footerItems', function (items: ItemList<Mithril.Children>) {
    const post = this.attrs.post;
    const discussion = post.discussion();

    if (post.contentType() !== 'comment' || !discussion.canSplit() || post.number() === 1) return;

    if (app.__fof_split.splitting && app.__fof_split.splittingFrom && post.number()! >= app.__fof_split.splittingFrom) {
      items.add('splitTo', [
        <Button
          icon="fas fa-code-branch"
          className="flagrow-split-endSplitButton Button Button--link"
          onclick={() => {
            app.modal.show(() => import('./components/SplitPostModal'), {
              post: post,
            });
          }}
        >
          {app.translator.trans('fof-split.forum.split.to')}
        </Button>,
        <Button
          icon="fas fa-times"
          className="flagrow-split-cancelSplitButton Button Button--link"
          onclick={() => {
            app.__fof_split.splitController?.reset();
            m.redraw();
          }}
        >
          {app.translator.trans('fof-split.forum.split.cancel')}
        </Button>,
      ]);
    }
  });
}
