##starts around 890 in screens.rpy

screen history():

    tag menu

    ## Avoid predicting this screen, as it can be very large.
    predict False

    use game_menu(_("History"), scroll=("vpgrid" if gui.history_height else "viewport"), yinitial=1.0):

        style_prefix "history"

        for h in _history_list:

            window:

                ## This lays things out properly if history_height is None.
                has fixed:
                    yfit True
                    screen history():

    tag menu

    ## Avoid predicting this screen, as it can be very large.
    predict False

    use game_menu(_("History"), scroll=("vpgrid" if gui.history_height else "viewport"), yinitial=1.0):

        style_prefix "history"

        for h in _history_list:

            window:

                ## This lays things out properly if history_height is None.
                has fixed:
                    yfit True
                    add "images/icon [h.who].png" xalign 0.01 yalign 0.5 #add this line


                frame:
                    background Frame("gui/round_frame.png", 15,15)
                    ymargin 4
                    yalign .5
                    xmargin 3
                    xpos gui.history_text_xpos
                    xsize gui.history_text_width
                    $ what = renpy.filter_text_tags(h.what, allow=gui.history_allow_tags)
                    text what:
                        substitute False
                        yalign .5

        if not _history_list:
            label _("The dialogue history is empty.")


## This determines what tags are allowed to be displayed on the history screen.add "images/icon [h.who].png" xalign 0.01 yalign 0.5 #add this line


                frame:
                    background Frame("gui/round_frame.png", 15,15)
                    ymargin 4
                    yalign .5
                    xmargin 3
                    xpos gui.history_text_xpos
                    xsize gui.history_text_width
                    $ what = renpy.filter_text_tags(h.what, allow=gui.history_allow_tags)
                    text what:
                        substitute False
                        yalign .5

        if not _history_list:
            label _("The dialogue history is empty.")


## This determines what tags are allowed to be displayed on the history screen.
