import FormModal, { IFormModalAttrs } from 'flarum/common/components/FormModal';
import Stream from 'flarum/common/utils/Stream';
import type Post from 'flarum/common/models/Post';
import type Mithril from 'mithril';
export interface SplitPostModalAttrs extends IFormModalAttrs {
    post: Post;
}
export default class SplitPostModal extends FormModal<SplitPostModalAttrs> {
    newDiscussionTitle: Stream<string>;
    oninit(vnode: Mithril.Vnode<SplitPostModalAttrs, this>): void;
    className(): string;
    title(): Mithril.Children;
    content(): Mithril.Children;
    onsubmit(e: Event): void;
}
