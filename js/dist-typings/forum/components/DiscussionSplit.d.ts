import EventPost from 'flarum/forum/components/EventPost';
export default class DiscussionSplit extends EventPost {
    /**
     * Get the name of the event icon.
     *
     * @return {String}
     */
    icon(): string;
    /**
     * Get the translation key for the description of the event.
     *
     * @return {String}
     */
    descriptionKey(): string;
    /**
     * Get the translation data for the description of the event.
     *
     * @return {Object}
     */
    descriptionData(): Record<string, any>;
}
