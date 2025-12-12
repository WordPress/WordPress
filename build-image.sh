#!/bin/bash
set -e

# DigitalOcean Container Registry
REGISTRY="${REGISTRY:-registry.digitalocean.com/scaleweb}"

# Git commit SHA for tagging
GIT_SHA=$(git rev-parse --short HEAD 2>/dev/null || echo "local")

# ArgoCD configuration
ARGOCD_APP="${ARGOCD_APP:-customer-wordpress}"
ARGOCD_NAMESPACE="${ARGOCD_NAMESPACE:-argocd}"

# Parse command line arguments
PUSH=true
SYNC_ARGOCD=false
IMAGE="wordpress"
dockerfile="Dockerfile"
name="$IMAGE"

echo "🐳 Building Docker images..."
echo "Registry: $REGISTRY"
echo "Git SHA: $GIT_SHA"
[ "$PUSH" = true ] && echo "📤 Will push images to registry"
[ "$SYNC_ARGOCD" = true ] && echo "🔄 Will sync ArgoCD application: $ARGOCD_APP"
echo ""

echo "📦 Building $name..."
docker build -f "$dockerfile" \
-t ${REGISTRY}/${name}:latest \
-t ${REGISTRY}/${name}:${GIT_SHA} \
.

if [ "$PUSH" = true ]; then
echo "📤 Pushing $name:latest..."
docker push ${REGISTRY}/${name}:latest
echo "📤 Pushing $name:${GIT_SHA}..."
docker push ${REGISTRY}/${name}:${GIT_SHA}
fi

echo ""
echo "✅ All images built successfully!"
echo ""
echo "📊 Image sizes:"
docker images ${REGISTRY}/* --format "table {{.Repository}}\t{{.Tag}}\t{{.Size}}" | grep -E "REPOSITORY|latest|${GIT_SHA}"

# Sync ArgoCD if requested
if [ "$SYNC_ARGOCD" = true ]; then
  echo "🔄 Syncing ArgoCD application: $ARGOCD_APP..."
  
  # Prefer kubectl method (uses kubeconfig, no separate ArgoCD auth needed)
  if command -v kubectl &> /dev/null; then
    echo "   Using kubectl to trigger ArgoCD sync (no separate auth required)..."
    
    # Check if application exists
    if ! kubectl get application "$ARGOCD_APP" -n "$ARGOCD_NAMESPACE" &>/dev/null; then
      echo "❌ ArgoCD application '$ARGOCD_APP' not found in namespace '$ARGOCD_NAMESPACE'"
      echo "   Available applications:"
      kubectl get applications -n "$ARGOCD_NAMESPACE" 2>/dev/null || echo "   (none found)"
      exit 1
    fi
    
    # Method 1: Use refresh annotation (works with automated sync policy)
    # This triggers ArgoCD to refresh and sync if automated sync is enabled
    echo "   Triggering refresh annotation..."
    if kubectl annotate application "$ARGOCD_APP" -n "$ARGOCD_NAMESPACE" \
      argocd.argoproj.io/refresh=hard \
      --overwrite 2>/dev/null; then
      echo "✅ ArgoCD refresh annotation applied!"
      echo "   With automated sync enabled, ArgoCD will sync automatically."
    else
      # Method 2: Try to patch operation directly (for manual sync)
      echo "   Attempting direct sync operation..."
      if kubectl patch application "$ARGOCD_APP" -n "$ARGOCD_NAMESPACE" \
        --type merge \
        -p='{"operation":{"initiatedBy":{"username":"build-script"},"sync":{"revision":"HEAD","syncStrategy":{"hook":{}}}}}' 2>/dev/null; then
        echo "✅ ArgoCD sync operation triggered!"
      else
        echo "⚠️  Could not trigger sync via kubectl."
        echo "   The application may have automated sync enabled (check argocd-apps/crm-system.yaml)"
        echo "   If automated sync is disabled, you may need to sync manually:"
        echo "   argocd login <argocd-server>"
        echo "   argocd app sync $ARGOCD_APP"
        echo ""
        echo "   OR via kubectl annotation:"
        echo "   kubectl annotate application $ARGOCD_APP -n $ARGOCD_NAMESPACE argocd.argoproj.io/refresh=hard --overwrite"
        # Don't exit with error - refresh annotation may still work with automated sync
      fi
    fi
  elif command -v argocd &> /dev/null; then
    # Fallback to ArgoCD CLI (requires authentication)
    echo "   Using ArgoCD CLI (kubectl not available)..."
    echo "   ⚠️  Note: You may need to login first: argocd login <argocd-server>"
    
    # Check if logged in
    if ! argocd app get "$ARGOCD_APP" &>/dev/null; then
      echo "❌ Not authenticated with ArgoCD. Please login first:"
      echo "   argocd login <argocd-server>"
      echo ""
      echo "   Alternatively, install kubectl and use kubeconfig (recommended):"
      echo "   kubectl annotate application $ARGOCD_APP -n $ARGOCD_NAMESPACE argocd.argoproj.io/refresh=hard --overwrite"
      exit 1
    fi
    
    argocd app sync "$ARGOCD_APP" --async || {
      echo "⚠️  ArgoCD sync failed. Token may be expired."
      echo "   Re-authenticate: argocd login <argocd-server>"
      exit 1
    }
    echo "✅ ArgoCD sync initiated via CLI!"
  else
    echo "❌ Neither kubectl nor argocd CLI found. Cannot sync ArgoCD."
    echo "   Please sync manually:"
    echo "   kubectl annotate application $ARGOCD_APP -n $ARGOCD_NAMESPACE argocd.argoproj.io/refresh=hard --overwrite"
    echo "   OR: argocd app sync $ARGOCD_APP"
    exit 1
  fi
  
  echo ""
  echo "   Monitor sync status:"
  echo "   kubectl get application $ARGOCD_APP -n $ARGOCD_NAMESPACE -w"
fi

echo ""
if [ "$PUSH" = false ]; then
  echo "💡 To push images, run with --push flag:"
  echo "   $0 --push"
fi

if [ "$SYNC_ARGOCD" = false ] && [ "$PUSH" = true ]; then
  echo "💡 To sync ArgoCD after pushing, run with --sync-argocd flag:"
  echo "   $0 --push --sync-argocd"
fi
