import EventPost from "flarum/components/EventPost";

export default class DiscussionSplit extends EventPost {

    /**
     * Get the name of the event icon.
     *
     * @return {String}
     */
    icon() {
        return 'code-fork';
    }

    /**
     * Get the translation key for the description of the event.
     *
     * @return {String}
     */
    descriptionKey() {
        return 'flagrow-split.forum.post.was_split';
    }

    /**
     * Get the translation data for the description of the event.
     *
     * @return {Object}
     */
    descriptionData() {
        return {
            'count': this.props.post.content()['count'],
            'target': <a className="EventPost-Split-target" href={this.props.content()['url']}
                         config={m.route}>{this.props.content()['name']}</a>
        };
    }
}
