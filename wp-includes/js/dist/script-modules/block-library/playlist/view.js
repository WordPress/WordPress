// packages/block-library/build-module/playlist/view.mjs
import { store, getContext, getElement } from "@wordpress/interactivity";
store(
  "core/playlist",
  {
    state: {
      playlists: {},
      get currentTrack() {
        const { currentId, playlistId } = getContext();
        if (!currentId || !playlistId) {
          return {};
        }
        const playlist = this.playlists[playlistId];
        if (!playlist) {
          return {};
        }
        return playlist.tracks[currentId] || {};
      },
      get isCurrentTrack() {
        const { currentId, uniqueId } = getContext();
        return currentId === uniqueId;
      }
    },
    actions: {
      changeTrack() {
        const context = getContext();
        context.currentId = context.uniqueId;
        context.isPlaying = true;
      },
      isPlaying() {
        const context = getContext();
        context.isPlaying = true;
      },
      isPaused() {
        const context = getContext();
        context.isPlaying = false;
      },
      nextSong() {
        const context = getContext();
        const currentIndex = context.tracks.findIndex(
          (uniqueId) => uniqueId === context.currentId
        );
        const nextTrack = context.tracks[currentIndex + 1];
        if (nextTrack) {
          context.currentId = nextTrack;
          const { ref } = getElement();
          setTimeout(() => {
            ref.play();
          }, 1e3);
        }
      }
    },
    callbacks: {
      autoPlay() {
        const context = getContext();
        const { ref } = getElement();
        if (context.currentId && context.isPlaying) {
          ref.play();
        }
      }
    }
  },
  { lock: true }
);