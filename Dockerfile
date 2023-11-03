#####################################################################
#                            Build Stage                            #
#####################################################################
FROM hugomods/hugo:exts as builder
# Base URL
ARG HUGO_BASEURL=
ENV HUGO_BASEURL=${HUGO_BASEURL}
ARG PLAUSIBLE_ENDPOINT
ARG HUGOxPARAMSxPLAUSIBLE_ANALYTICSxENDPOINT="${PLAUSIBLE_ENDPOINT}"

# Build site
COPY . /src
RUN npm install
RUN hugo --minify --gc && cp -r ./node_modules/@twemoji/svg /src/public/emojis/
# Set the fallback 404 page if defaultContentLanguageInSubdir is enabled, please replace the `en` with your default language code.
# RUN cp ./public/en/404.html ./public/404.html

#####################################################################
#                            Final Stage                            #
#####################################################################
FROM hugomods/hugo:nginx
# Copy the generated files to keep the image as small as possible.
COPY --from=builder /src/public /site
