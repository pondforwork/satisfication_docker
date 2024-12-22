INSERT INTO `satisfication_test`.`checkin_settings`
(`checkin_settings_id`,
`advance_late_duration`)
VALUES
(1,15);


INSERT INTO `satisfication_test`.`client_settings`
(`client_settings_id`,
`wallpaper_url`,
`header_text`,
`header_font_size`,
`header_text_color`,
`footer_text`,
`footer_font_size`,
`footer_text_color`,
`last_updated`)
VALUES
(1,
'123456.jpg',
'ทดสอบ',
null,
null,
'footer_text',
null,
null,
'2024-09-12 12:25:46');

INSERT INTO `satisfication_test`.`sequence`
(`sequence_id`,
`sequence_name`,
`prefix`,
`last_order`)
VALUES
(1,
'location',
'L',
0);

INSERT INTO `satisfication_test`.`sequence`
(`sequence_id`,
`sequence_name`,
`prefix`,
`last_order`)
VALUES
(2,
'counter',
'D',
0);

INSERT INTO `satisfication_test`.`role`
(`role_id`,
`role`)
VALUES
(1,
'admin');

INSERT INTO `satisfication_test`.`role`
(`role_id`,
`role`)
VALUES
(2,
'employee');

INSERT INTO `satisfication_test`.`role`
(`role_id`,
`role`)
VALUES
(3,
'executive');

