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
| `form.name.required` | bool | `true` | Whether the name field is required. | |
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
| `custom-field` | array(array(string => string \| bool \| Closure)) | `array()` | Custom field definitions. | |
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
    <h3>Comment by <?php echo $comment->name() ?></h3>
    <div class="message"><?php echo $comment->message() ?></div>
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
- Use Kirby Comments *name* and *value* methods for accessing the name and value of fields and buttons.
- Include a message field.
- Include a hidden field with the session ID as value.
- Include a preview and submit button, where the submit button is shown for valid previews only.

A name field is required by default, but you can remove this constraint by adding `c::set('comments.form.name.required', false);` to your config.php.

The following minimal example fulfills all of the requirements above.

```html
<form method="post" accept-charset="utf-8">
  <label for="name">Name</label>
  <input type="text" id="name" name="<?php echo $comments->nameName() ?>" value="<?php echo $comments->nameValue() ?>">
  
  <label for="message">Message</label>
  <textarea id="message" name="<?php echo $comments->messageName() ?>"><?php echo $comments->messageValue() ?></textarea>
  
  <input type="hidden" name="<?php echo $comments->sessionIdName() ?>" value="<?php echo $comments->sessionId() ?>">
  
  <input type="submit" name="<?php echo $comments->previewName() ?>" value="Preview">
  <?php if ($comments->isValidPreview()): ?>
    <input type="submit" name="<?php echo $comments->submitName() ?>" value="Submit">
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
  <input type="text" name="<?php echo $comments->honeypotName() ?>" value="<?php echo $comments->honeypotValue() ?>">
</div>
```

If you do not use a honeypot, disable it in your config.php using `c::set('comments.honeypot.enabled', false);`.

Kirby Comments will block any comment submission with a honeypot value other than an empty string. You can change that by setting the `honeypot.human-value` option to any string you like. You can also change the name of the field using the `form.honeypot` option.

#### Jumping to the Comment

When submitting the form, the page will reload and your scroll position is lost. You can make the browser scroll automatically to the new comment by giving the new comment a unique ID and referencing it in the forms `action` attribute.

```html
<?php foreach ($comments as $comment): ?>
  <div id="comment-<?php echo $comment->id() ?>">
    ...
  </div>
<?php endforeach ?>

<form action="#comment-<?php echo $comments->nextCommentId() ?>" ...>
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

`Comments` instances manage the comments of a specific Kirby page. This involves processing HTTP POST data for creating comment previews, submitting comments, storing comments as Kirby pages, reading those pages and reporting status.

```php
$comments = $page->comments();
```

#### `$comments->process() : CommentsStatus`

Processes comments based on the HTTP POST data of the HTTP request. This involves generating preview comments, storing published comments as Kirby pages, creating comments pages, validating user data and sending email notifications.

This method may be called multiple times during a single HTTP request but execute only once. On repeated calls, the current status object is returned.

#### `$comments->isEmpty() : bool`

`true` iff no comment is managed by this `Comments` instance.

#### `$comments->count() : integer`

Number of comments managed `true` by this `Comments` instance.

#### `$comments->nextCommentId() : integer`

The ID of the preview comment in case of a preview; the ID of the next, as of yet unwritten, comment otherwise. IDs start at 1 and increment on a per-page basis by 1.

#### `$comments->isSuccessfulSubmission() : bool`

`true` iff the user has submitted a comment and no errors occurred.

#### `$comments->value($name : string, $default="" : string) : string`

Returns the HTML-escaped value of the HTTP POST data with the name `$name`.

When a user submits a form, the page is reloaded and all fields in the form are cleared. In order to keep the text that the user has typed into the fields, you have to set the `value` attribute of all input fields to the value which was transmitted by the forms request.

This method helps you in doing so by returning `$default` if the form was not submitted, or an HTML-escaped version of the value posted by the user.

```html
<input ... value="<?php echo $comments->value($comments->emailName()) ?>">
```

#### `$comments->nameValue($default="" : string) : string`

Convenience method for accessing `$this->value()` for the name field.

#### `$comments->emailValue($default="" : string) : string`

Convenience method for accessing `$this->value()` for the email field.

#### `$comments->websiteValue($default="" : string) : string`

Convenience method for accessing `$this->value()` for the website field.

#### `$comments->messageValue($default="" : string) : string`

Convenience method for accessing `$this->value()` for the message field.

#### `$comments->customFieldValue($field_name : string, $default="" : string) : string`

Convenience method for accessing `$this->value()` for custom fields.

#### `$comments->honeypotValue($default="" : string) : string`

Convenience method for accessing `$this->value()` for the honeypot field.

#### `$comments->submitName() : string`

HTTP POST name of the submit button. Used as the key for the HTTP POST data and as the value of the HTML input `name` attribute. The value is defined by the `form.submit` option.

#### `$comments->previewName() : string`

HTTP POST name of the preview button. Used as the key for the HTTP POST data and as the value of the HTML input `name` attribute. The value is defined by the `form.preview` option.

#### `$comments->nameName() : string`

HTTP POST name of the name field. Used as the key for the HTTP POST data and as the value of the HTML input `name` attribute. The value is defined by the `form.name` option.

#### `$comments->requiresName() : bool`

`true` iff a comment author has to provide an email address. Corresponding option: `form.name.required`.

#### `$comments->nameMaxLength() : integer`

Maximum allowed number of characters in the comment’s name field. Corresponding option: `form.name.max-length`.

#### `$comments->emailName() : string`

HTTP POST name of the email field. Used as the key for the HTTP POST data and as the value of the HTML input `name` attribute. The value is defined by the `form.email` option.

#### `$comments->requiresEmailAddress() : bool`

`true` iff a comment author has to provide an email address. Corresponding option: `form.name.required`.

#### `$comments->emailMaxLength() : integer`

Maximum allowed number of characters in the comment’s email field. Corresponding option: `form.email.max-length`.

#### `$comments->websiteName() : string`

HTTP POST name of the website field. Used as the key for the HTTP POST data and as the value of the HTML input `name` attribute. The value is defined by the `form.website` option.

#### `$comments->websiteMaxLength() : integer`

Maximum allowed number of characters in the comment’s website field. Corresponding option: `form.website.max-length`.

#### `$comments->messageName() : string`

HTTP POST name of the message field. Used as the key for the HTTP POST data and as the value of the HTML input `name` attribute. The value is defined by the `form.message` option.

#### `$comments->messageMaxLength() : integer`

Maximum allowed number of characters in the comment’s message field. Corresponding option: `form.message.max-length`.

#### `$comments->customFieldName($field_name : string) : string|null`

HTTP POST name of a custom field. Used as the key for the HTTP POST data and as the value of the HTML input `name` attribute. `null` iff no custom field with the name `$field_name` exists.

#### `$comments->honeypotName() : string`

HTTP POST name of the honeypot field. Used as the key for the HTTP POST data and as the value of the HTML input `name` attribute. The value is defined by the `form.honeypot` option.

#### `$comments->isUsingHoneypot() : bool`

`true` iff the honeypot mechanism is enabled. Corresponding option: `honeypot.enabled`.

#### `$comments->sessionIdName() : string`

HTTP POST name of the session ID field. Used as the key for the HTTP POST data and as the value of the HTML input `name` attribute. The value is defined by the `form.session_id` option.

#### `$comments->sessionId() : string`

ID of the current comments session.

#### `$comments->isValidPreview() : bool`

`true` iff the current request is a preview and the preview is valid.

#### `$comments->toArray() : Comment[]`

Returns the comments managed by this `Comments` instance sorted in chronological order.

### `$comment : Comment`

A `Comment` object stores information about the comment author, the comment’s message, and metadata like the publication date and the comment ID.

Additionally, it provides methods for accessing its values in unescaped and escaped form for rendering templates.

```html
<?php foreach ($comments as $comment): ?>
  ...
<?php endforeach ?>
```

#### `$comment->id() : integer`

Unique identifier of the comment. The first comment on a page has an ID of 1, incrementing by 1 per page.

#### `$comment->name() : string`

HTML escaped name of the comment author. `""` iff no name was specified.

#### `$comment->rawName() : string|null`

Unescaped name of the comment author. `null` iff no name was specified. **May contain unescaped HTML code; use with caution!**

#### `$comment->email() : string`

HTML escaped email address of the comment author. `""` iff no email address was specified.

#### `$comment->rawEmail() : string|null`

Unescaped email address on the comment author. `null` iff no email address was specified. **May contain unescaped HTML code; use with caution!**

#### `$comment->website() : string`

HTML escaped website address of the comment author. `""` iff no website address was specified.

#### `$comment->rawWebsite() : string`

Unescaped website address of the comment author. `null` iff no website address was specified. **May contain unescaped HTML code; use with caution!**

#### `$comment->message() : string`

Formatted message which is processed using Markdown and optionally SmartyPants. Only HTML tags allowed by the `form.message.allowed_tags` option are kept. HTML tags and HTML entities included in the message are escaped before applying the Markdown formatter.

#### `$comment->rawMessage() : string`

Unprocessed message. **May contain unescaped HTML/Markdown code; use with caution!**

#### `$comment->customFields() : CommentsField[]`

List of custom fields.

#### `$comment->customField($fieldName : string) : mixed|null`

Unescaped value of the custom field with the name `$fieldName`. `null` if no custom field with the name `$fieldName` exists.

#### `$comment->date($format="Y-m-d" : string) : string`

Formatted date and/or time of the publication of the comment. The value of `$format` must match a pattern for PHP’s [`DateTime::format` method](http://php.net/manual/de/datetime.format.php).

#### `$comment->datetime() : DateTime`

Date and time of the publication of the comment.

#### `$comment->page() : Page`

Page on which the comment was posted or if the comment is in preview mode, the page on which the comment is previewed.

#### `$comment->isPreview() : bool`

`true` iff the comment is in preview mode and not stored as Kirby page.

#### `$comment->isLinkable() : bool`

`true` iff the website address of the comment author is not `null`.

### `$status : CommentsStatus`

A status describes the state of an object after an operation and can either be a success status or an error status. While only one success code (status code 0) exists, multiple errors codes (split into multiple domains) help to describe the exact nature of the problem.

```php
$status = $comments->process();
```

#### `$status->getCode() : integer`

Status code indicating the type of status. Have a look at the documentation for a complete table of status codes and their meaning.

| Domain | Code | Description |
|---|---|---|
| Success | 0 | Success. |
| System | 100 | Expected comment ID of type integer. |
| System | 101 | Expected comment ID of value greater than 0. |
| System | 102 | Could not construct `Comment` from page. |
| Developer | 200 | Could not create comments page. |
| Developer | 201 | Could not create comment page. |
| Developer | 202 | Could not read email template file. |
| Developer | 203 | Could not send email notification about new comment. |
| Developer | 204 | Custom field without name attribute. |
| User | 300 | The current session is invalid. |
| User | 301 | The name field is required. (Only when requiring name.) |
| User | 302 | The name is too long. |
| User | 303 | The email address field is required. (Only when requiring email address.) |
| User | 304 | The email address must be valid. (Only when requiring email address or not empty.) |
| User | 305 | The email address is too long. |
| User | 306 | The website address field must not contain JavaScript code. |
| User | 307 | The website address is too long. |
| User | 308 | The message field is required. |
| User | 309 | The message is too long. |
| User | 310 | The comment must be written by a human being. |
| User | 311 | Invalid custom field. |
| User | 312 | The custom field is required. |
| User | 313 | The custom field is too long. |
| Custom | 4xx | Custom exceptions. |

You can either show the user the default message (`$status->getMessage()`) or provide your own status descriptions by checking the code in a `switch` statement.

#### `$status->getMessage() : string`

Message of the exception that caused this status iff `$this->exception` is not `null`; string describing the status code as such otherwise.

#### `$status->getException() : Exception`

Exception that has caused the status. `null` iff the status is not based upon an exception.

#### `$status->isUserError() : bool`

`true` iff the status is in the User or Custom domain.

#### `$status->isError() : bool`

`true` iff the status is not in the Success domain.

### `$type : CommentsFieldType`

Defines the name, title, HTTP POST name, properties of custom fields, and provides validation and sanitization mechanisms. Custom fields are constructed from an associative array and placed in your config.php.

```php
c::set('comments.custom-fields', array(
  array(
    'name' => 'my_custom_field',
    'title' => 'My Custom Field',
    'httpPostName' => 'my_custom_field',
    'required' => true,
    'max-length' => 32,
    'validate' => function ($value, $page) {
      ...
    },
    'sanitize' => function ($value, $page) {
      ...
    }
  )
));
```

The array must include a `name` key pointing to a string and can additionally contain any of the following key-value pairs:

- `title : string`: If unset, `name` is used.
- `httpPostName : string`: If unset, `name` is used.
- `required : bool`: Defaults to `false`.
- `max-length : integer`: Defaults to `128`.
- `validate : Closure`: Defaults to `null`.
- `sanitize : Closure`: Defaults to `null`.

#### `name : string`

Name of the field type. Must be usable as a YAML object key.

#### `title : string`

Title of the field type. Describes the field type with one or two words.

#### `httpPostName : string`

Name used to identify fields of this type over HTTP POST.

#### `required : bool`

`true` iff the value of fields of this type may not be an empty string or missing from the HTTP POST data.

#### `max-length : integer`

Maximum allowed number of characters in the field.

#### `validate : function ($value : string, $page : Page) : string`

Validates the value of a field of this type. This closure receives the field’s value as its first argument. Returns `true` for valid values, throws exceptions with a code in the range of 400-499 for known validation errors and returns `false` for unknown validation errors. Note that this closure is called after Kirby Comments’s validation (which checks `required` and `max-length`).

If `null`, a return value of `true` is assumed.

#### `sanitize : function ($value : string, $page, Page) : mixed|null`

Sanitizes the value of a field of this type. This closure receives the field’s value as its first argument and must return a value. Note that this closure is called after Kirby Comments’s validation and after `validate`.

If `null`, a return value of `$value` is assumed.
