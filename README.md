# Kirby Comments

## Status Codes

| Domain | Code | Description |
|---|---|---|
| – | 0 | Success |
| System | 100 | ID must be of type `"int"`. |
| System | 101 | ID must be greater than 0. |
| System | 102 | Could not create `Comment` from page. |
| Developer | 200 | Could not create comments page. |
| Developer | 201 | Could not create comment page. |
| Developer | 202 | Could not read email template file. |
| Developer | 203 | Could not send email. |
| User | 300 | Session is invalid. |
| User | 301 | Name field must not be empty. |
| User | 302 | Email field must not be empty. (Only when requiring email-address.) |
| User | 303 | Email-address must be valid. (Only when requiring email-address.) |
| User | 304 | Website field must not contain JavaScript code. |
| User | 305 | Message field must not be empty. |
| User | 306 | Message is to long. |