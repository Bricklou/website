ASSETS_LIST = (node_modules/@twemoji/svg)
TARGET_BIN_LIST = (assets/emojis)

all: build copy

build:
	@echo "Building..."
	@hugo

copy:
	@echo "Copying..."
	@cp -r node_modules/@twemoji/svg/ public/emojis/

serve:
	@echo "Serving..."
	@hugo server -D --disableFastRender