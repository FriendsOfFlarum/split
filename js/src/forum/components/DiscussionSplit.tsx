import EventPost from 'flarum/forum/components/EventPost';
import Link from 'flarum/common/components/Link';

interface SplitContent {
  toNew?: boolean;
  count: number;
  url: string;
  title: string;
}

export default class DiscussionSplit extends EventPost {
  /**
   * Get the name of the event icon.
   *
   * @return {String}
   */
  icon(): string {
    return 'fas fa-code-branch';
  }

  /**
   * Get the translation key for the description of the event.
   *
   * @return {String}
   */
  descriptionKey(): string {
    const content = this.attrs.post.content() as unknown as SplitContent;
    if (content['toNew']) {
      return 'fof-split.forum.post.was_split_to';
    }

    return 'fof-split.forum.post.was_split_from';
  }

  /**
   * Get the translation data for the description of the event.
   *
   * @return {Object}
   */
  descriptionData(): Record<string, any> {
    const content = this.attrs.post.content() as unknown as SplitContent;

    return {
      count: content['count'],
      target: (
        <Link className="EventPost-Split-target" href={content['url']}>
          {content['title']}
        </Link>
      ),
    };
  }
}
