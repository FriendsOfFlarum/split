import {extend} from "flarum/extend";
import app from "flarum/app";
import PostControls from "flarum/utils/PostControls";
import Button from "flarum/components/Button";
import CommentPost from "flarum/components/CommentPost";
import SplitPostModal from "./components/SplitPostModal";

export default function (controller) {

    extend(PostControls, 'moderationControls', function(items, post) {
        const discussion = post.discussion();

        if (post.contentType() !== 'comment' || !discussion.canSplit() || post.number() == 1) return;

        items.add('splitFrom', [
            m(Button, {
                icon: 'fas fa-code-branch',
                className: 'flagrow-split-startSplitButton',
                onclick: () => {
                    controller.start(post.id(), post.number());
                }
            }, app.translator.trans('fof-split.forum.split.from'))
        ]);
    });

    extend(CommentPost.prototype, 'footerItems', function(items) {
        const post = this.props.post;
        const discussion = post.discussion();

        if (post.contentType() !== 'comment' || !discussion.canSplit() || post.number() == 1) return;

        items.add('splitTo', [
            m(Button, {
                icon: 'fas fa-code-branch',
                className: 'flagrow-split-endSplitButton Button Button--link',
                onclick: () => {
                    controller.end(post.number());
                    var splitModal = new SplitPostModal();
                    splitModal.setController(controller);
                    app.modal.show(splitModal);
                },
                style: {display: 'none'}
            }, app.translator.trans('fof-split.forum.split.to'))
        ]);
    });
}
