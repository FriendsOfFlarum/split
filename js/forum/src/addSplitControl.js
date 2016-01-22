import { extend } from 'flarum/extend';
import app from 'flarum/app';
import PostControls from 'flarum/utils/PostControls';
import Button from 'flarum/components/Button';
import CommentPost from 'flarum/components/CommentPost';
import DiscussionPage from 'flarum/components/DiscussionPage';

import SplitPostModal from 'flagrow/split/components/SplitPostModal';

export default function() {

    extend(PostControls, 'moderationControls', function(items, post) {
        const discussion = post.discussion();

        if (post.isHidden() || post.contentType() !== 'comment' || !discussion.canSplit()) return;

        items.add('splitFrom', [
            m(Button, {
                icon: 'code-fork',
                onclick: () => {app.current.splitting = true; m.redraw()},
                className: 'flagrow-split-startSplitButton',
            }, app.translator.trans('flagrow-split.forum.post_controls.split_button')),
        ]);
    });

    extend(CommentPost.prototype, 'footerItems', function(items) {
        const post = this.props.post;
        const discussion = post.discussion();

        if (post.isHidden() || post.contentType() !== 'comment' ||  !discussion.canSplit()) return;

        var isSplitting = () => {console.log('hi'); return app.current.splitting};


        items.add('splitTo', [
            m(Button, {
                icon: 'code-fork',
                className: 'flagrow-split-endSplitButton Button Button--link',
                onclick: () => {app.current.splitting = false},
                // @todo the above is a temporary test solution, we need to implement the modal
                //onclick: () => app.modal.show(new SplitPostModal(post)),
                style: {display: ( isSplitting() ? "block" : "none")},
            }, app.translator.trans('flagrow-split.forum.post_footer.split_button'))
        ]);
    });
}
