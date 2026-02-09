// routes/template/route.ts
import { redirect } from "@wordpress/route";
var route = {
  beforeLoad: () => {
    const isTemplateActivateEnabled = typeof window !== "undefined" && window.__experimentalTemplateActivate;
    throw redirect({
      throw: true,
      to: "/templates/list/$activeView",
      params: {
        activeView: isTemplateActivateEnabled ? "active" : "all"
      }
    });
  }
};
export {
  route
};
