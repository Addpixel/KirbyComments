# Kirby Comments

File based comments stored as subpages. Easy to setup. Easy to use. Flexible as hell. [Live-Demo](http://kirby-comments.addpixel.net/projects/comments-demo).

## Features

- file-based
- email-notifications for new comments
- preview comments before submitting them
- use honeypot to prevent spam
- tons of options

## Installation

1. Move the folder “comments” to site/plugins.
2. Move all files inside of “snippets” to site/snippets.

## Usage

To use the `comments` snippet, include it on the page where the comments and the comments form should appear on.

```php
<div class="comments">
  <?php snippet('comments') ?>
</div>
```

## Options

Options may be set by calling `c::set('comments.OPTION_NAME', $value)` in your config.php.

```php
c::set('comments.use.email', true);
c::set('comments.email.to', array('my-email@address.com'));
```

| Name | Type | Default | Description |
|---|---|---|---|
| `comments-page.title` | string | `"Comments"` | Title of a comments page. |
| `comments-page.dirname` | string | `"comments"` | Name of the folder of a comments page. |
| `comments-page.templat` | string | `"comments"` | Name of the template of a comments page. |
| `comment-page.dirname` | string | `"comment"` | Name of the folder of a comment page. |
| `comment-page.template` | string | `"comment"` | Name of the template of a comment page. |
| `form.submit` | string | `"submit"` | POST name of the submit-button. |
| `form.preview` | string | `"preview"` | POST name of the preview button. |
| `form.name` | string | `"name"` | POST name of the name field. |
| `form.email` | string | `"email"` | POST name of the email address field. |
| `form.website` | string | `"website"` | POST name of the website address field. |
| `form.message` | string | `"message"` | POST name of the message field. |
| `form.honeypot` | string | `"subject",` | POST name of the honeypot field. |
| `form.session_id` | string | `"session_id"` | POST name of the session id field. |
| `session.key` | string | `"comments"` | Name of a comments-session. |
| `require.email` | bool | `false` | Whether the email field is required. |
| `use.honeypot` | bool | `true` | Whether the system should use a honeypot. |
| `use.email` | bool | `false` | Whether the system should send email notifications. |
| `allowed_tags` | string | `"<p><br><a><em><strong><code><pre>"` | All HTML tags that are allowed in a comment’s message. |
| `max-character-count` | integer | `1000` | Maximum number of characters in the message. |
| `max-field-length` | integer | `64` | Maximum number of characters in the name/email/website field. |
| `human-honeypot-value` | string | `""` | Value of an empty honeypot field. |
| `email.to` | array(string) | `array()` | List of email addresses that receive email notifications. |
| `email.subject` | string | `"New Comment on {{ page.title }}"` | Subject of an email notification. |
| `email.undefined-value` | string | `"(not specified)"` | Text that is inserted whenever a value for a email notification is undefined. |
| `setup.page.title_key` | string | `"title"` | The key/name of the title of a page. This is used to access the title of a page for email notifications. |

## Email Placeholders

The email body (email.template.txt) and subject (option: `email.subject`) can contain placeholders which will be replaced with the corresponding value. Placeholders have a name, start with `{{` and end with `}}`.

```
You’ve received a new comment on “{{ page.title }}” by {{ comment.user.name }}.
```

| Name | Description |
|---|---|
| `comment.user.name`| Name of the commentator. |
| `comment.user.email` | Email address of the commentator. |
| `comment.user.website` | Website address of the commentator. |
| `comment.message` | Message, that the commentator has posted. |
| `page.title` | Title of the page the comment was posted on. |
| `page.url` | URL of the page the comment was posted on. |

## Status Codes

| Domain | Code | Description |
|---|---|---|
| – | 0 | Success. |
| System | 100 | ID must be of type `"integer"`. |
| System | 101 | ID must be greater than 0. |
| System | 102 | Could not create `Comment` from page. |
| Developer | 200 | Could not create comments page. |
| Developer | 201 | Could not create comment page. |
| Developer | 202 | Could not read email template file. |
| Developer | 203 | Could not send email. |
| User | 300 | Session is invalid. |
| User | 301 | Name field must not be empty. |
| User | 302 | Name is too long. |
| User | 303 | Email field must not be empty. (Only when requiring email address.) |
| User | 304 | Email address must be valid. (Only when requiring email address.) |
| User | 305 | Email address is too long. |
| User | 306 | Website field must not contain JavaScript code. |
| User | 307 | Website address is too long. |
| User | 308 | Message field must not be empty. |
| User | 309 | Message is too long. |
| User | 310 | Commentator must be human. |
