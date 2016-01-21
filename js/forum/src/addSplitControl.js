import { extend } from 'flarum/extend';
import app from 'flarum/app';
import PostControls from 'flarum/utils/PostControls';
import Button from 'flarum/components/Button';
import CommentPost from 'flarum/components/CommentPost';
import DiscussionPage from 'flarum/components/DiscussionPage';

import SplitPostModal from 'flagrow/split/components/SplitPostModal';

export default function() {

    extend(PostControls, 'moderationControls', function(items, post) {
        var discussion = post.discussion();
        if (post.isHidden() || post.contentType() !== 'comment' || !discussion.canSplit()) return;
        items.add('splitFrom', [
            m(Button, {
                icon: 'code-fork',
                onclick: discussion.isSplitting.bind(this, true),
                className: 'flagrow-split-startSplitButton',
            }, app.translator.trans('flagrow-split.forum.post_controls.split_button')),
        ]);
    });

    extend(CommentPost.prototype, 'footerItems', function(items) {
        var post = this.props.post;
        var discussion = post.discussion();
        console.log(discussion.isSplitting);
        if (post.isHidden() || post.contentType() !== 'comment' ||  !discussion.canSplit()) return;
        items.add('splitTo', [
            m(Button, {
                icon: 'code-fork',
                className: 'flagrow-split-endSplitButton',
                onclick: discussion.isSplitting.bind(this, false),
                //onclick: () => app.modal.show(new SplitPostModal(post)),
                style: {display: (discussion.isSplitting() === true ? "block" : "none")}
            }, app.translator.trans('flagrow-split.forum.post_footer.split_button'))
        ]);
    });
}
