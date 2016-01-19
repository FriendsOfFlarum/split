import { extend } from 'flarum/extend';
import app from 'flarum/app';
import PostControls from 'flarum/utils/PostControls';
import Button from 'flarum/components/Button';
import CommentPost from 'flarum/components/CommentPost';
import DiscussionPage from 'flarum/components/DiscussionPage';

import SplitPostModal from 'flagrow/split/components/SplitPostModal';

export default function() {

    var splitting = m.prop();
    splitting(false);

    extend(PostControls, 'moderationControls', function(items, post) {
        if (post.isHidden() || post.contentType() !== 'comment' || !post.canSplit()) return;

        items.add('splitFrom', [
            m(Button, {
                icon: 'code-fork',
                onclick: function() {splitting(true);}.bind(this)
            }, app.translator.trans('flagrow-split.forum.post_controls.split_button')),
        ]);
    });

    extend(CommentPost.prototype, 'footerItems', function(items) {
        items.add('splitTo', [
            m(Button, {
                icon: 'code-fork',
                //onclick: () => app.modal.show(new SplitPostModal(post)),
                style: {display: (splitting() === true ? 'block' : 'none')}
            }, app.translator.trans('flagrow-split.forum.post_footer.split_button'))
        ]);
    });


}
