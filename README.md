# Kirby Comments

File based comments stored as subpages. Easy to setup. Easy to use. Flexible as hell. [Live-Demo](https://kirby-comments.addpixel.net/demos/comments).

<a href="https://kirby-comments.addpixel.net/demos/comments"><img src="https://kirby-comments.addpixel.net/kirbycomments.svg" alt></a>

## Features

- [X] file-based
- [X] Kirby CLI installation & update support
- [X] email-notifications for new comments
- [X] preview comments before submitting them
- [X] use honeypot to prevent spam
- [X] block cross-site request forgery
- [X] tons of options
- [X] blueprints

## Installation

The recommended installation process is using the [Kirby CLI](https://github.com/getkirby/cli).

```sh
$ kirby plugin:install Addpixel/KirbyComments
```

### Manuel Installation

1. Download [the latest release](https://github.com/Addpixel/KirbyCommentsPlugin/releases) as a zip-file.
2. Decompress the zip-file and rename the folder to “comments”.
3. Move the folder “comments” into site/plugins.

## Usage

The Kirby Comments plugin comes with an example snippet (`comments`) which lists all comments of a page and provided a form for submitting new ones.

To use the `comments` snippet, include it on the page where the comments and the comments form should appear.

```php
<div class="comments">
  <?php snippet('comments') ?>
</div>
```

You are not limited to using the `comments` snipped shipped with this plugin. Feel free to learn from [the source code](https://github.com/Addpixel/KirbyComments/blob/master/snippets/comments.php) and write your own comments-form if the `comments` snippet doesn’t suit your needs.

## Options

Options may be set by calling `c::set('comments.OPTION_NAME', $value)` in your config.php.

```php
c::set('comments.use.email', true);
c::set('comments.email.to', array('my-email@address.com'));
```

| Name | Type | Default | Description | * |
|---|---|---|---|---|
| `comments-page.title` | string | `"Comments"` | Title of a comments page. | |
| `comments-page.dirname` | string | `"comments"` | Name of the folder of a comments page. | |
| `comments-page.template` | string | `"comments"` | Name of the blueprint/template of a comments page. | |
| `comment-page.dirname` | string | `"comment"` | Name of the folder of a comment page. | |
| `comment-page.template` | string | `"comment"` | Name of the blueprint/template of a comment page. | |
| `comments-snippet` | string | `"comments"` | Name of the default comments snippet. | ✓ |
| `form.submit` | string | `"submit"` | POST name of the submit-button. | ✓ |
| `form.preview` | string | `"preview"` | POST name of the preview button. | ✓ |
| `form.name` | string | `"name"` | POST name of the name field. | ✓ |
| `form.email` | string | `"email"` | POST name of the email address field. | ✓ |
| `form.website` | string | `"website"` | POST name of the website address field. | ✓ |
| `form.message` | string | `"message"` | POST name of the message field. | ✓ |
| `form.honeypot` | string | `"subject"` | POST name of the honeypot field. | ✓ |
| `form.session_id` | string | `"session_id"` | POST name of the session id field. | ✓ |
| `session.key` | string | `"comments"` | Name of a comments-session. | ✓ |
| `require.email` | bool | `false` | Whether the email field is required. | ✓ |
| `use.honeypot` | bool | `true` | Whether the system should use a honeypot. | ✓ |
| `allowed_tags` | string | `"<p><br><a><em><strong><code><pre>"` | All HTML tags that are allowed in a comment’s message. | ✓ |
| `max-character-count` | integer | `1000` | Maximum number of characters in the message. | ✓ |
| `max-field-length` | integer | `64` | Maximum number of characters in the name/email/website field. | ✓ |
| `human-honeypot-value` | string | `""` | Value of an empty honeypot field. | ✓ |
| `use.email` | bool | `false` | Whether the system should send email notifications. | ✓ |
| `email.to` | array(string) | `array()` | List of email addresses that receive email notifications. | ✓ |
| `email.subject` | string | `"New Comment on {{ page.title }}"` | Subject of an email notification. | ✓ |
| `email.undefined-value` | string | `"(not specified)"` | Text that is inserted whenever a value for an email notification is undefined. | ✓ |
| `setup.page.title_key` | string | `"title"` | The key/name of the title of a page. This is used to access the title of a page for email notifications. | ✓ |

\* These options may be modified while comments are stored on the site. Options, which do no have a check-mark in this column, may only be modified whenever no comments are stored on the site (before receiving any comments or after having deleted all comments and comments pages).

## Email Notifications

To use email notifications enable the `use.email` option and specify at least one recipient using the `email.to` option.

The email body (email.template.txt) and subject (option: `email.subject`) can contain placeholders which will be replaced with the corresponding value. Placeholders have a name, start with `{{` and end with `}}`.

```
You’ve received a new comment on “{{ page.title }}” by {{ comment.user.name }}.
```

If you want to customize the contents of the email body, create a new file assets/plugins/comments/email.template.txt (create folders as needed). This location is based on [the recommended plugin assets location](https://getkirby.com/docs/developer-guide/plugins/assets#customizing-plugin-assets).

| Name | Description |
|---|---|
| `comment.user.name`| Name of the commentator. |
| `comment.user.email` | Email address of the commentator. |
| `comment.user.website` | Website address of the commentator. |
| `comment.message` | Message, that the commentator has posted. |
| `page.title` | Title of the page the comment was posted on. |
| `page.url` | URL of the page the comment was posted on. |

## Custom Markup

The Kirby Comments plugin ships with a simple `comments` snippet. It is designed to handle most use cases and to serve as example implementation. If you find yourself constrained by its markup or functionality, you can create a custom comments snippet.

### Basics

All comments are handled by the comments object. Kirby offers variables like `$site` and `$page` as an interface to its functionality. In the same way, the Kirby Comments plugin allows you to access all of its functionality by creating the `$comments` object based on a Kirby page.

```php
<?php $comments = new Comments($page) ?>
```

### File Structure

The `$page` is referring to the page holding the comments. Like this:

```
content/
└─ blog/
   └─ 1-hello-world/ <- $page
      ├─ post.txt
      └─ comments/
         ├─ comments.txt
         ├─ 1-comment-1/
         │  └─ comment.txt
         ├─ 2-comment-2/
         │  └─ comment.txt
         └─ 3-comment-3/
            └─ comment.txt
``` 

As you can see, the comment are simply stored as subpages, grouped in a hidden comments directory. The naming scheme of the comment directories was chosen to provide the following functionalities:

- `#-` makes the comments visible and orders it.
- `comment-#` gives the comment a unique address, at which it can be located. This means, that every comment has a public URL like: www.example.org/blog/hello-world/comments/comment-1. If you would like, you can create `comments` and `comment` templates to style them. By default, both the comments list (some-page/comments) and the comments individual pages (some-page/comments/comment-1) are not referenced by any link, so no users will reach those pages.

### Handling New Comments

OK, so you have created a `$comments` object. The first thing you should do is to handle new comment requests. This is done by calling the `process` method.

```php
<?php $status = $comments->process() ?>
```

This will do a lot for you. It checks, whether any new comment was submit to the page or if the user has requested a preview. For security reasons the Kirby Comments plugin will check for a valid session key. Every time the comments form is rendered by PHP, the comments plugin creates generates a new session id and stores it in a hidden input field.

```php
<input type="hidden"
       name="<?= $comments->sessionIdName() ?>"
       value="<?= $comments->sessionId() ?>">
```

You have to include this hidden field in you form. For every page refresh (including form submission) a new id will be generated. This ensures that no duplicated comment is submitted to the page by refreshing the page after having submitted a comment.

In order to submit comments which are processable by the plugin, please respect the following guidelines.

1. Submit via POST (`<form ... method="post">`)
2. Submit as UTF-8 (`<form ... accept-charset="utf-8">`)
3. Do no chose the `name` in the markup, but in the plugins options. This means that code such as `<input type="submit" name="my-preview-button">` should be replaced by `<input type="submit" name="<?= $comments->previewName() ?>">`. This way, the plugin knows what names to look for. If you want to customize the name, register the new name in your config.php, like `c::set('comments.form.preview', 'my-preview-button');`. All available options are listed in the Options table above.
4. Hide the submit (not preview) button until the preview is valid. (`<?php if ($comments->validPreview()): ?><input type="submit" ...><?php endif ?>`)
5. Do not show any form when a user has successfully submitted a comment. You can check for that state by looking at `$comments->userHasSubmitted()`.

Before implementing your own form, have a look the default `comments` snippet in snippets/comments.php.

## API Documentation

### `$comments`

```php
$comments = new Comments($page);
```

#### `$comments->process()`

Processes the HTTP-POST data and creates new comments or generates preview comments. These comments are added to the list of comments contained by `$comments`.

#### `$comments->isEmpty() : bool`

Whether `$comments` contains any comments (preview comments included).

#### `$comments->nextCommentId() : integer`

All comments have a per-page unique id. The first comment has an id of 1, the second an id of 2, … This method returns the id of the next comment. The *next* comment is the *next after all stored comments*, so previewing a comment does not change this value as the preview comment is described by the next-comment-id.

#### `$comments->userHasSubmitted() : bool`

Whether the user has successfully submitted a new comment.

#### `$comments->value($name : string) : string`

This is a convenience method for creating forms. When a user submits a form, the page is reloaded and the form is cleared. In order to prevent this, you have to set the `value` attribute of all input fields to the value which was transmitted by the forms request. This method helps you in doing so, by returning an empty string, if the form was not submitted, or the value posted by the user.

```php
<input ... value="<?= $comments->value($comments->websiteName()) ?>">
```

#### `$comments->submitName() : string`

The HTTP-POST name for the submit button. The value is defined by the `form.submit` option.

#### `$comments->previewName() : string`

The HTTP-POST name for the preview button. The value is defined by the `form.preview` option.

#### `$comments->nameName() : string`

The HTTP-POST name for the name field. The value is defined by the `form.name` option. Yo dawg!

#### `$comments->emailName() : string`

The HTTP-POST name for the email field. The value is defined by the `form.email` option.

#### `$comments->websiteName() : string`

The HTTP-POST name for the website field. The value is defined by the `form.website` option.

#### `$comments->messageName() : string`

The HTTP-POST name for the website field. The value is defined by the `form.message` option.

#### `$comments->honeypotName() : string`

The HTTP-POST name for the honeypot field. The value is defined by the `form.honeypot` option.

#### `$comments->sessionIdName() : string`

The HTTP-POST name for the session id field. The value is defined by the `form.session_id` option.

#### `$comments->isUsingHoneypot() : bool`

Whether the plugin should check the value of a honeypot field. This behaviour can be determined using the `use.honeypot` option. The HTTP-POST name of the honeypot field is defined by the `form.honeypot` option.

#### `$comments->requiresEmailAddress() : bool`

Whether the plugin requires a (valid) email address. This behaviour can be determined using the `require.email` option. The HTTP-POST name of the email field is defined by the `form.email` option.

#### `$comments->messageMaxlength() : integer`

Maximum number of characters allowed in the message field. Corresponding option: `max-character-count`. 

#### `$comments->fieldMaxlength() : integer`

Maximum number of characters allowed in the name, email and website field. Corresponding option: `max-field-length`.

#### `$comments->sessionId() : string`

The current session id.

#### `$comments->validPreview() : bool`

Whether the current preview is valid. `false`, if no preview is performed.

### `$comment`

```php
<?php foreach ($comments as $comment): ?>
  ...
<?php endforeach ?>>
```

#### `$comment->id() : integer`

The per-page unique identifier of the comment. Ids start at 1, not a 0.

#### `$comment->name() : string`

The name of the author of the comment.

#### `$comment->email() : string`

The email address of the author of the comment. `null` if no email address was provided.

#### `$comment->website() : string`

The address of the website of the author of the comment. `null` if no website address was provided.

#### `$comment->message() : string`

The message of the comment. May contain HTML code, which is limited to the HTML-tags specified by the `allowed_tags` option.

#### `$comment->rawMessage() : string`

The message of the comment. May contain markdown-like formatting instructions.

#### `$comment->date($format='Y-m-d') : DateTime`

The point in time of when the comment was posted.

#### `$comment->isPreview() : bool`

Whether the comment is a preview. Iff `false`, the comment was loaded from the file system.

#### `$comment->isLinkable() : bool`

Whether `$comment->website()` is `null`.

### `$status`

```php
$status = $comments->process();
```

#### `$status->getCode() : integer`

The status code which describes state of the `$comments` object.

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

You can either show the user the default message (`$status->getMessage()`) or provide your own status descriptions by checking its code in a `switch` statement.

#### `$status->getMessage() : string`

A short, english description of the status. May not be user-friendly.

#### `$status->getException() : Exception`

The exception responsible for the status. May be `null`, if no exception was involved in defining the status.

#### `$status->isUserError() : bool`

Whether the status was defined by illegal behaviour by the user. The status code is in the *User* domain.

#### `$status->isError() : bool`

Whether the status represents an error. The status code is >= 100.
