<?php if(!defined('KIRBY')) exit ?>

title: Comments
pages: true
files: false
icon: comments
fields:
  title:
    label: Title
    type: text
    required: true
  text:
    label: Text
    required: true
    type: textarea