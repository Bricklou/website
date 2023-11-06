ASSETS_LIST = (node_modules/@twemoji/svg)
TARGET_BIN_LIST = (assets/emojis)

all: build copy

build:
	@echo "Building..."
	@hugo

copy:
	@echo "Copying..."
	@mkdir -p public/assets/emojis
	@cp -r node_modules/@twemoji/svg/*.svg public/assets/emojis/

serve:
	@echo "Serving..."
	@hugo server -D --disableFastRender