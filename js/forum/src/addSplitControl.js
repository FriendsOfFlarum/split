import { extend } from 'flarum/extend';
import app from 'flarum/app';
import PostControls from 'flarum/utils/PostControls';
import Button from 'flarum/components/Button';

//import SplitPostModal from 'flagrow/split/components/SplitPostModal';


export default function() {
    extend(PostControls, 'userControls', function(items, post) {
        if (post.isHidden() || post.contentType() !== 'comment' || !post.canSplit()) return;

        items.add('split', [
            m(Button, {
                icon: 'code-fork'
                //onclick: () => app.modal.show(new SplitPostModal({post}))
            }, app.translator.trans('flagrow-split.forum.post_controls.split_button'))
        ]);
    });
}
