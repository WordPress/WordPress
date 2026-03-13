"use strict";
var wp;
(wp ||= {}).undoManager = (() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __commonJS = (cb, mod) => function __require() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
    // If the importer is in node compatibility mode or this is not an ESM
    // file that has been converted to a CommonJS file using a Babel-
    // compatible transform (i.e. "__esModule" has not been set), then set
    // "default" to the CommonJS "module.exports" for node compatibility.
    isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
    mod
  ));
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // package-external:@wordpress/is-shallow-equal
  var require_is_shallow_equal = __commonJS({
    "package-external:@wordpress/is-shallow-equal"(exports, module) {
      module.exports = window.wp.isShallowEqual;
    }
  });

  // packages/undo-manager/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    createUndoManager: () => createUndoManager
  });
  var import_is_shallow_equal = __toESM(require_is_shallow_equal());
  function mergeHistoryChanges(changes1, changes2) {
    const newChanges = { ...changes1 };
    Object.entries(changes2).forEach(([key, value]) => {
      if (newChanges[key]) {
        newChanges[key] = { ...newChanges[key], to: value.to };
      } else {
        newChanges[key] = value;
      }
    });
    return newChanges;
  }
  var addHistoryChangesIntoRecord = (record, changes) => {
    const existingChangesIndex = record?.findIndex(
      ({ id: recordIdentifier }) => {
        return typeof recordIdentifier === "string" ? recordIdentifier === changes.id : (0, import_is_shallow_equal.default)(recordIdentifier, changes.id);
      }
    );
    const nextRecord = [...record];
    if (existingChangesIndex !== -1) {
      nextRecord[existingChangesIndex] = {
        id: changes.id,
        changes: mergeHistoryChanges(
          nextRecord[existingChangesIndex].changes,
          changes.changes
        )
      };
    } else {
      nextRecord.push(changes);
    }
    return nextRecord;
  };
  function createUndoManager() {
    let history = [];
    let stagedRecord = [];
    let offset = 0;
    const dropPendingRedos = () => {
      history = history.slice(0, offset || void 0);
      offset = 0;
    };
    const appendStagedRecordToLatestHistoryRecord = () => {
      const index = history.length === 0 ? 0 : history.length - 1;
      let latestRecord = history[index] ?? [];
      stagedRecord.forEach((changes) => {
        latestRecord = addHistoryChangesIntoRecord(latestRecord, changes);
      });
      stagedRecord = [];
      history[index] = latestRecord;
    };
    const isRecordEmpty = (record) => {
      const filteredRecord = record.filter(({ changes }) => {
        return Object.values(changes).some(
          ({ from, to }) => typeof from !== "function" && typeof to !== "function" && !(0, import_is_shallow_equal.default)(from, to)
        );
      });
      return !filteredRecord.length;
    };
    return {
      addRecord(record, isStaged = false) {
        const isEmpty = !record || isRecordEmpty(record);
        if (isStaged) {
          if (isEmpty) {
            return;
          }
          record.forEach((changes) => {
            stagedRecord = addHistoryChangesIntoRecord(
              stagedRecord,
              changes
            );
          });
        } else {
          dropPendingRedos();
          if (stagedRecord.length) {
            appendStagedRecordToLatestHistoryRecord();
          }
          if (isEmpty) {
            return;
          }
          history.push(record);
        }
      },
      undo() {
        if (stagedRecord.length) {
          dropPendingRedos();
          appendStagedRecordToLatestHistoryRecord();
        }
        const undoRecord = history[history.length - 1 + offset];
        if (!undoRecord) {
          return;
        }
        offset -= 1;
        return undoRecord;
      },
      redo() {
        const redoRecord = history[history.length + offset];
        if (!redoRecord) {
          return;
        }
        offset += 1;
        return redoRecord;
      },
      hasUndo() {
        return !!history[history.length - 1 + offset];
      },
      hasRedo() {
        return !!history[history.length + offset];
      }
    };
  }
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
