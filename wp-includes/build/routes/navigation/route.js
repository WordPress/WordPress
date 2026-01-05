// routes/navigation/route.ts
import { redirect } from "@wordpress/route";
var route = {
  beforeLoad: () => {
    throw redirect({ to: "/navigation/list" });
  }
};
export {
  route
};
