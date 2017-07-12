# Kirby Comments

File-based comments stored as subpages. Easy to setup. Easy to use. Flexible as hell. [Live-Demo](https://kirby-comments.addpixel.net/demos/comments).

[![](https://kirby-comments.addpixel.net/kirbycomments.svg)](https://kirby-comments.addpixel.net/demos/comments)

## Features

- [X] file-based
- [X] Kirby CLI installation & update support
- [X] email notifications for new comments
- [X] preview comments before submitting them
- [X] use a honeypot to prevent spam
- [X] block cross-site request forgery
- [X] standard, accessible markup
- [X] tons of options
- [X] blueprints

## Installation

The recommended installation process is using the [Kirby CLI](https://github.com/getkirby/cli).

```sh
$ kirby plugin:install Addpixel/KirbyComments
```

### Manual Installation

1. Download [the latest release](https://github.com/Addpixel/KirbyComments/releases) as a zip-file.
2. Decompress the zip file and rename the folder to *comments*.
3. Move the folder *comments* into site/plugins.

When updating, simply delete the old *comments* folder and replace it with the new one.

## Usage

Kirby Comments includes three snippets: `comments-form`, `comments-list` and `comments`.

- `comments-form` renders an HTML form for writing comments,
- `comments-list` lists all comments and
- `comments` is a convenience snippet for rendering both the list and the form.

A quick and easy installation could use the following code:

```html
<div class="comments">
  <?php snippet('comments') ?>
</div>
```

You can also place the form and list separately:

```html
<div class="comments-list">
  <?php snippet('comments-list') ?>
</div>
...
<div class="comments-form">
  <?php snippet('comments-form') ?>
</div>
```

You are by no means limited to the snippets shipped with Kirby Comments. For creating your own form or comments list I recommend having a look at the following sections:

- [Kirby Comments options](#options)
- [Custom markup](#custom-markup)
- [Kirby Comments API](#api-documentation)
- [source code of the snippets included in Kirby Comments](https://github.com/Addpixel/KirbyComments/tree/master/snippets)

## Options

Options may be set by calling `c::set('comments.OPTION_NAME', $value)` in your site/config/config.php.

```php
// Enable email notifications
c::set('comments.email.enabled', true);
c::set('comments.email.to', array('name@example.org'));

// Customize the title of the comments page
c::set('comments.pages.comments.title', function ($page) {
  return 'Kommentare zu „' . $page->title() . '“';
});
```

| Name | Type | Default | Description | * |
|---|---|---|---|---|
| `pages.comments.title` | Closure | `function ($page) { return 'Comments for “' . $page->title() . '”'; }` | Takes a `Page` on which a comment was posted and returns the title for the comments page as a `string`. | |
| `pages.comments.dirname` | string | `"comments"` | Name of the folder of a comments page. | * |
| `pages.comments.template` | string | `"comments"` | Name of the template and blueprint of a comments page. | * |
| `pages.comment.dirname` | string | `"comment"` | Name of the folder of a comment page. | * |
| `pages.comment.template` | string | `"comment"` | Name of the template and blueprint of a comment page. | * |
| `pages.comment.visible` | bool | `true` | Whether comment pages are visible (have an index-prefix). | * |
| `form.submit` | string | `"submit"` | POST name of the submit button. | |
| `form.preview` | string | `"preview"` | POST name of the preview button. | |
| `form.name` | string | `"name"` | POST name of the name field. | |
| `form.name.max-length` | integer | `64` | Maximum length of the value in the name field. | |
| `form.email` | string | `"email"` | POST name of the email address field. | |
| `form.email.required` | type | `false` | Whether the email address field is required. | |
| `form.email.max-length` | integer | `64` | Maximum length of the value in the email address field. | |
| `form.website` | string | `"website"` | POST name of the website field. | |
| `form.website.max-length` | integer | `64` | Maximum length of the value in the website field. | |
| `form.message` | string | `"message"` | POST name of the message field. | |
| `form.message.allowed_tags` | string | `"<p><br><a><em><strong><code><pre>"` | HTML tags that are allowed in a comment’s message. | |
| `form.message.smartypants` | bool | `true` | Whether to apply [SmartyPants](https://daringfireball.net/projects/smartypants/) to comment messages. Requires the [global smartypants option](https://getkirby.com/docs/cheatsheet/options/smartypants) to be `true`. | |
| `form.message.max-length` | integer | `1024` | Maximum length of the value in the message field. | |
| `form.session_id` | string | `"session_id"` | POST name of the session ID field. | |
| `form.honeypot` | string | `"subject"` | POST name of the honeypot field. | |
| `honeypot.enabled` | bool | `true` | Whether the plugin should use a honeypot. | |
| `honeypot.human-value` | string | `""` | Value of an empty honeypot field. | |
| `email.enabled` | bool | `false` | Whether the plugin should send email notifications. | |
| `email.to` | array(string) | `array()` | List of email addresses that receive email notifications. | |
| `email.subject` | string | `"New Comment on {{ page.title }}"` | Subject of an email notification. | |
| `email.undefined-value` | string | `"(not specified)"` | Text that is inserted for values that the comment’s author did not specify. | |
| `session.key` | string | `"comments"` | Key used to store the comments session. | |
| `setup.content-page.title` | Closure | `function ($page) { return $page->title(); }` | Takes a `Page` and returns its title as `string`. Is used for generating email notifications. | |

\* Can not be changed after the first comment was published on the site.

## Email Notifications

Kirby Comments can notify you about new comments. Set the `email.enabled` option to `true` and specify at least one recipient using the `email.to` option.

- **email subject:** value of the `email.subject` option
- **email body:** contents of assets/email.template.txt. Do not edit the email.template.txt file in the plugins folder as it will be replaced when updating Kirby Comments. Instead, create a new file at yoursite/assets/plugins/comments/email.template.txt (create folders as needed). This location is based on [the recommended plugin assets location](https://getkirby.com/docs/developer-guide/plugins/assets#customizing-plugin-assets).

The email’s body and subject can contain placeholders which will be replaced with the corresponding value. Placeholders have a name, start with `{{` and end with `}}`.

```
You’ve received a new comment on “{{ page.title }}” by {{ comment.user.name }}.
```

| Name | Description |
|---|---|
| `comment.user.name`| Name of the commentator. |
| `comment.user.email` | Email address of the commentator. |
| `comment.user.website` | Website of the commentator. |
| `comment.message` | Message of the comment. |
| `page.title` | Title of the page the comment was posted on. |
| `page.url` | URL of the page on which the comment was posted. |

## Custom Markup

As described in [Usage](#usage), Kirby Comments ships with three snippets: `comments-form`, `comments-list` and `comments`. These make it easy to get started, as you don’t have to write your own form or comment list.

However, you are not limited to only using these three snippets. This section describes how you can write your own comments form and list.

### Accessing the Kirby Comments API

All comments are handled by the `$comments` object. Kirby offers variables like `$site` and `$page` as an interface to its functionality. In the same way, Kirby Comments allows you to access its functionality using the `$comments` variable.

```php
$comments = $page->comments();
```

### File Structure

`$page` in the above code example is referring to the page containing the comments page. A typical file structure looks like this:

```
content/
└─ blog/
   └─ 1-hello-world/        <- $page
      ├─ post.txt
      └─ comments/          <- comments page
         ├─ comments.txt
         ├─ 1-comment-1/    <- comment page
         │  └─ comment.txt
         ├─ 2-comment-2/
         │  └─ comment.txt
         └─ 3-comment-3/
            └─ comment.txt
``` 

Comments are stored as subpages, grouped in a hidden directory called “comments” (option `pages.comments.dirname`). This page contains the individual comment pages. The name of a comment page can be split into three pieces:

- `#-`: Makes the comment pages visible within the comments page. Can be removed by setting the `pages.comment.visible` option to `false`.
- `comment`: Name of all comment pages. Can be changed using the `pages.comment.dirname` option.
- `-#`: Orders comment pages and gives them a unique ID. This way, every comment has its own page located at `www.example.org/blog/hello-world/comments/comment-#`.

### Custom Markup: Processing Comments

Before listing comments or rendering a form you should call `$comments->process()`. This will handle new comments submitted using the form and report a success or error status.

```php
$status = $comments->process();
```

You can read more about the comment’s status in the [API documentation](#status--commentsstatus).

### Custom Markup: Comments List

`$comments` implements PHP’s `Iterator` interface so that you can loop over every comment using a `foreach` loop.

```html
<?php foreach ($comments as $comment): ?>
  <div class="comment">
    <h3>Comment by <?= $comment->name() ?></h3>
    <div class="message"><?= $comment->message() ?></div>
  </div>
<?php endforeach ?>
```

If you want to add a heading before the comments, you can also check whether there are any comments.

```html
<?php if (!$comments->isEmpty()): ?>
  <h2>Comments</h2>
  
  <?php foreach ($comments as $comment): ?>
    ...
  <?php endforeach ?>
<?php endif ?>
```

For a list of all `$comment` methods have a look at the [API Documentation](#comment--comment).

### Custom Markup: Comments Form

Your form must fulfill the following criteria:

- Submit as UTF-8 via HTTP POST
- Submit to a page with a comments form (or simply to the same page)
- Use Kirby Comments “name” and “value” methods for accessing the name and value of fields and buttons.
- Include a name and message field.
- Include a hidden field with the session ID as value.
- Include a preview and submit button, where the submit button is shown for valid previews only.

The following minimal example fulfills all of the above.

```html
<form method="post" accept-charset="utf-8">
  <label for="name">Name</label>
  <input type="text" id="name" name="<?= $comments->nameName() ?>" value="<?= $comments->nameValue() ?>">
  
  <label for="message">Message</label>
  <textarea id="message" name="<?= $comments->messageName() ?>"><?= $comments->messageValue() ?></textarea>
  
  <input type="hidden" name="<?= $comments->sessionIdName() ?>" value="<?= $comments->sessionId() ?>">
  
  <input type="submit" name="<?= $comments->previewName() ?>" value="Preview">
  <?php if ($comments->validPreview()): ?>
    <input type="submit" name="<?= $comments->submitName() ?>" value="Submit">
  <?php endif ?>
</form>
```

Note: The session ID ensures that no duplicated comment is submitted to the page by refreshing the page after having submitted a comment and prevents cross-site request forgery.

For security reasons and to prevent spam, you should not render the form after a comment was submitted. For this, wrap your form in an `if` block checking `$comments->userHasSubmitted()`.

```html
<?php if ($comments->userHasSubmitted()): ?>
  <p>Thank you for your comment!</p>
<?php else: ?>
  <form ...>
    ...
  </form>
<?php endif ?>
```

#### Using a Honeypot

By default, Kirby Comments will look for a honeypot value. Generally, a honeypot is a trap that checks for bots trying to submit comments. In the case of Kirby Comments, you include a field and hide it with CSS (most bots don’t load and apply CSS). Humans will not see the field and therefore not fill it out. Bots on the other hand mostly only read the HTML source code and fill out every field the find with whatever data they think fits best.

To use the honeypot, include the following in your form:

```html
<div class="hide-me">
  <input type="text" name="<?= $comments->honeypotName() ?>" value="<?= $comments->honeypotValue() ?>">
</div>
```

If you do not use a honeypot, disable it in your config.php using `c::set('comments.honeypot.enabled', false);`.

Kirby Comments will block any comment submission with a honeypot value other than an empty string. You can change that by setting the `honeypot.human-value` option to any string you like. You can also change the name of the field using the `form.honeypot` option.

#### Jumping to the Comment

When submitting the form, the page will reload and your scroll position is lost. You can make the browser scroll automatically to the new comment by giving the new comment a unique ID and referencing it in the forms `action` attribute.

```html
<?php foreach ($comments as $comment): ?>
  <div id="comment-<?= $comment->id() ?>">
    ...
  </div>
<?php endforeach ?>

<form action="#comment-<?= $comments->nextCommentId() ?>" ...>
  ...
</form>
```

If you don’t want every comment to have an ID you can check for `$comment->isPreview()`.

```html
<?php foreach ($comments as $comment): ?>
  <div<? e($comment->isPreview(), ' id="preview-comment"') ?>>
    ...
  </div>
<?php endforeach ?>

<form action="#preview-comment" ...>
  ...
</form>
```

## API Documentation

### `$comments : Comments implements Iterator, Countable`

The main object you will be using when working with Kirby Comments.

```php
$comments = $page->comments();
```

#### `$comments->process() : CommentsStatus`

Processes the HTTP-POST data and creates new comments or generates preview comments. These comments are added to the list of comments contained by `$comments`.

This method may be called multiple times, but only executes once. The `CommentsStatus` object is returned on every call.

#### `$comments->isEmpty() : bool`

Whether `$comments` contains any comments (preview comments included).

#### `$comments->count() : integer`

The number of comments managed by `$comments`. Comments loaded by calling `$comments->process()` (unpublished preview comments) are included.

#### `$comments->nextCommentId() : integer`

All comments have a per-page unique ID. The first comment has an ID of 1, the second an ID of 2, … This method returns the ID of the next comment. The *next* comment is the *next after all stored comments*, so previewing a comment does not change this value as the preview comment is described by the next-comment-id.

#### `$comments->userHasSubmitted() : bool`

Whether the user has successfully submitted a new comment.

#### `$comments->value($name : string, $default="" : string) : string`

When a user submits a form, the page is reloaded and all fields in the form are cleared. In order to keep the text that the user has typed into the fields, you have to set the `value` attribute of all input fields to the value which was transmitted by the forms request.

This method helps you in doing so, by returning `$default` if the form was not submitted, or an HTML-escaped version of the value posted by the user.

```html
<input ... value="<?= $comments->value($comments->websiteName()) ?>">
```

#### `$comments->nameValue($default="" : string) : string`

Convenience method for `$comments->value($comments->nameName(), $default)`.

#### `$comments->emailValue($default="" : string) : string`

Convenience method for `$comments->value($comments->emailName(), $default)`.

#### `$comments->websiteValue($default="" : string) : string`

Convenience method for `$comments->value($comments->websiteName(), $default)`.

#### `$comments->messageValue($default="" : string) : string`

Convenience method for `$comments->value($comments->messageName(), $default)`.

#### `$comments->honeypotValue($default="" : string) : string`

Convenience method for `$comments->value($comments->honeypotName(), $default)`.

#### `$comments->submitName() : string`

The HTTP-POST name for the submit button. The value is defined by the `form.submit` option.

#### `$comments->previewName() : string`

The HTTP-POST name for the preview button. The value is defined by the `form.preview` option.

#### `$comments->nameName() : string`

The HTTP-POST name for the name field. The value is defined by the `form.name` option. Yo dawg!

#### `$comments->nameMaxLength() : integer`

Maximum number of characters allowed in the name field. Corresponding option: `form.name.max-length`.

#### `$comments->emailName() : string`

The HTTP-POST name for the email field. The value is defined by the `form.email` option.

#### `$comments->requireEmailAddress() : bool`

Whether the plugin requires a (valid) email address. This behavior can be determined using the `form.email.required` option. The HTTP-POST name of the email field is defined by the `form.email` option.

#### `$comments->emailMaxLength() : integer`

Maximum number of characters allowed in the email address field. Corresponding option: `form.email.max-length`.

#### `$comments->websiteName() : string`

The HTTP-POST name for the website field. The value is defined by the `form.website` option.

#### `$comments->websiteMaxLength() : integer`

Maximum number of characters allowed in the website field. Corresponding option: `form.website.max-length`.

#### `$comments->messageName() : string`

The HTTP-POST name for the website field. The value is defined by the `form.message` option.

#### `$comments->messageMaxLength() : integer`

Maximum number of characters allowed in the message field. Corresponding option: `form.message.max-length`. 

#### `$comments->sessionIdName() : string`

The HTTP-POST name for the session ID field. The value is defined by the `form.session_id` option.

#### `$comments->sessionId() : string`

The current session ID.

#### `$comments->honeypotName() : string`

The HTTP-POST name for the honeypot field. The value is defined by the `form.honeypot` option.

#### `$comments->isUsingHoneypot() : bool`

Whether the plugin should check the value of a honeypot field. This behavior can be determined using the `honeypot.enabled` option. The HTTP-POST name of the honeypot field is defined by the `form.honeypot` option.

#### `$comments->validPreview() : bool`

Whether the current preview is valid. `false`, if no preview is performed.

#### `$comments->toArray() : array`

Returns an array containing all comments managed by `$comments` in chronological order.

### `$comment : Comment`

An individual comment.

```html
<?php foreach ($comments as $comment): ?>
  ...
<?php endforeach ?>
```

#### `$comment->id() : integer`

The per-page unique identifier of the comment. IDs start at 1, not at 0.

#### `$comment->name() : string`

The name of the author of the comment.

#### `$comment->email() : string`

The email address of the author of the comment. `null` if no email address was specified.

#### `$comment->website() : string`

The address of the website of the author of the comment. `null` if no website was specified.

#### `$comment->message() : string`

The message of the comment. May contain HTML code, which is limited to the HTML-tags specified by the `form.message.allowed_tags` option.

#### `$comment->rawMessage() : string`

The message of the comment. May contain Markdown instructions and/or HTML code.

#### `$comment->date($format="Y-m-d") : string`

The point in time when the comment was posted formatted as a string.

#### `$comment->datetime() : DateTime`

The point in time when the comment was posted.

#### `$comment->isPreview() : bool`

Whether the comment is a preview. Iff `false`, the comment was loaded from the file system.

#### `$comment->isLinkable() : bool`

Whether `$comment->website()` is not `null`.

### `$status : CommentsStatus`

```php
$status = $comments->process();
```

#### `$status->getCode() : integer`

The status code which describes the state of the `$comments` object.

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
| User | 304 | Email address must be valid. (Only when requiring email address or not empty.) |
| User | 305 | Email address is too long. |
| User | 306 | Website field must not contain JavaScript code. |
| User | 307 | Website address is too long. |
| User | 308 | Message field must not be empty. |
| User | 309 | Message is too long. |
| User | 310 | Commentator must be human. |

You can either show the user the default message (`$status->getMessage()`) or provide your own status descriptions by checking its code in a `switch` statement.

#### `$status->getMessage() : string`

A short, English description of the status. May not be user-friendly.

#### `$status->getException() : Exception`

The exception responsible for the status. Can be `null`, if no exception was involved in defining the status.

#### `$status->isUserError() : bool`

Whether the status was defined by illegal behavior by the user. The status code is in the *User* domain.

#### `$status->isError() : bool`

Whether the status represents an error. The status code is `>= 100`.