import EventPost from "flarum/components/EventPost";

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
        if (this.props.post.content()['toNew']) {
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
            'count': this.props.post.content()['count'],
            'target': <a className="EventPost-Split-target" href={this.props.post.content()['url']}
                         config={m.route}>{this.props.post.content()['title']}</a>
        };
    }
}
