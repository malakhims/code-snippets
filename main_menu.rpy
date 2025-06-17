## Main Menu screen ############################################################
##
## Used to display the main menu when Ren'Py starts.
##
## https://www.renpy.org/doc/html/screen_special.html#main-menu

screen main_menu( ):

# This ensures that any other menu screen is replaced.
    tag menu

    add "/gui/main_menu.png"  # Add this line to include the background image


    vbox:
        style_prefix "quick"
        spacing .65

        xalign 0.03
        yalign 0.66
        
        imagebutton auto "gui/start_%s.png" action Start()
        imagebutton auto "gui/sidestory_%s.png" action Start('sidestory')
        imagebutton auto "gui/load_%s.png" action ShowMenu('load')
        imagebutton auto "gui/options_%s.png"action ShowMenu('preferences')
        imagebutton auto "gui/exit_%s.png" action  Quit()

