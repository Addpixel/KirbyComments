<?php if(!defined('KIRBY')) exit ?>

title: Comment
pages: false
files: false
icon: comment
fields:
  name:
    label: Name
    type: text
    required: true
    width: 1/2
    icon: user
    validate:
      max: 64
  date:
    label: Date
    type: datetime
    width: 1/2
    validate:
      date
  email:
    label: Email Address
    type: email
    width: 1/2
    validate:
      max: 64
      email
  url:
    label: Website Address
    type: url
    width: 1/2
    validate:
      max: 64
  text:
    label: Text
    required: true
    type: textarea
    validate:
      max: 1000