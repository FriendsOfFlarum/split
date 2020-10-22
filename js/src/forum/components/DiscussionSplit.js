import EventPost from 'flarum/components/EventPost';
import Link from 'flarum/components/Link';

export default class DiscussionSplit extends EventPost {
    /**
     * Get the name of the event icon.
     *
     * @return {String}
     */
    icon() {
        return 'fas fa-code-branch';
    }

    /**
     * Get the translation key for the description of the event.
     *
     * @return {String}
     */
    descriptionKey() {
        if (this.attrs.post.content()['toNew']) {
            return 'fof-split.forum.post.was_split_to';
        }

        return 'fof-split.forum.post.was_split_from';
    }

    /**
     * Get the translation data for the description of the event.
     *
     * @return {Object}
     */
    descriptionData() {
        return {
            count: this.attrs.post.content()['count'],
            target: (
                <Link className="EventPost-Split-target" href={this.attrs.post.content()['url']}>
                    {this.attrs.post.content()['title']}
                </Link>
            ),
        };
    }
}
